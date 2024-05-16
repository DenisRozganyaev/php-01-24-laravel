<?php

namespace App\Services\Payments;

use App\Enums\PaymentSystem;
use App\Enums\TransactionStatus;
use App\Http\Requests\Ajax\CreateOrderRequest;
use App\Repositories\Contract\OrderRepositoryContract;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal;

class PaypalService implements Contract\PaypalServiceContract
{
    protected Paypal $paypalClient;

    public function __construct(protected OrderRepositoryContract $orderRepository)
    {
        $this->paypalClient = app(PayPal::class); //  X new Paypal() X
        $this->paypalClient->setApiCredentials(config('paypal'));
        $this->paypalClient->setAccessToken($this->paypalClient->getAccessToken());
    }

    public function create(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $paypalOrder = $this->paypalClient->createOrder($this->buildOrderRequestData());
            $data = array_merge(
                $request->validated(),
                [
                    'vendor_order_id' => $paypalOrder['id'],
                    'total' => Cart::instance('cart')->total()
                ]
            );

            $order = $this->orderRepository->create($data);
            DB::commit();

            return response()->json($order);
        } catch (\Exception $exception) {
            DB::rollBack();
            logs()->error($exception);
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }

    public function capture(string $vendorOrderId): JsonResponse
    {
        try {
            DB::beginTransaction();

            $result = $this->paypalClient->capturePaymentOrder($vendorOrderId);

            $order = $this->orderRepository->setTransaction(
              $vendorOrderId,
              PaymentSystem::Paypal,
                $this->convertedStatus($result['status'])
            );

            $result['id'] = $order->id;
            $result['vendorOrderId'] = $vendorOrderId;
            DB::commit();

            return response()->json($result);
        } catch (\Exception $exception) {
            DB::rollBack();
            logs()->error($exception);
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }

    protected function convertedStatus(string $status): TransactionStatus
    {
        return match ($status) {
            'COMPLETED', 'APPROVED' => TransactionStatus::Success,
            'CREATED', 'SAVED' => TransactionStatus::Pending,
            default => TransactionStatus::Canceled
        };
    }

    protected function buildOrderRequestData(): array
    {
        $subtotal = Cart::instance('cart')->subtotal();
        $total = Cart::instance('cart')->total();
        $tax = Cart::instance('cart')->tax();
        $cart = Cart::instance('cart')->content();
        $items = [];

        $cart->each(function ($cartItem) use (&$items) {
            $items[] = [
                'name' => $cartItem->name,
                'quantity' => $cartItem->qty,
                'sku' => $cartItem->model->SKU,
                'url' => url(route('products.show', $cartItem->model)),
                'category' => 'PHYSICAL_GOODS',
                'image_url' => str_replace('http', 'https', $cartItem->model->thumbnailUrl),
                'unit_amount' => [
                    'value' => $cartItem->price,
                    'currency_code' => config('paypal.currency'),
                ],
                'tax' => [
                    'value' => round($cartItem->price / 100 * $cartItem->taxRate, 2),
                    'currency_code' => config('paypal.currency'),
                ],
            ];
        });

        return [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => config('paypal.currency'),
                        'value' => $total,
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => config('paypal.currency'),
                                'value' => $subtotal
                            ],
                            'tax_total' => [
                                'value' => $tax,
                                'currency_code' => config('paypal.currency'),
                            ]
                        ]
                    ],
                    'items' => $items,
                ]
            ],
        ];
    }
}
