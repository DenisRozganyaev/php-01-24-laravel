<?php

namespace App\Services\Payments;

use App\Http\Requests\Ajax\CreateOrderRequest;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Mockery\MockInterface;
use Srmklive\PayPal\Services\PayPal;

class PaypalService implements Contract\PaypalServiceContract
{
    protected Paypal $paypalClient;

    public function __construct()
    {
        $this->paypalClient = app(PayPal::class); //  X new Paypal() X
        $this->paypalClient->setApiCredentials(config('paypal'));
        $this->paypalClient->setAccessToken($this->paypalClient->getAccessToken());
    }

    public function create(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $subtotal = Cart::instance('cart')->subtotal();
            $tax = Cart::instance('cart')->tax();
            $cart = Cart::instance('cart')->content();
            $items = [];

            $cart->each(function($cartItem) use (&$items) {
               $items[] = [
                   'name' => $cartItem->name,
                   'quantity' => $cartItem->qty,
                   'sku' => $cartItem->model->SKU,
                   'url' => url(route('products.show', $cartItem->model)),
                   'category' => 'PHYSICAL_GOODS',
                   'image_url' => $cartItem->model->thumbnailUrl,
                   'unit_amount' => $cartItem->price,
                   'tax' => $cartItem->price / 100 * $cartItem->taxRate,
               ];
            });

            $paypalOrder = $this->paypalClient->createOrder([
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    [
                        'amount' => [
                            'currency_code' => config('paypal.currency'),
                            'value' => $subtotal,
                            'breakdown' => [
                                'tax_total' => [
                                    'value' => $tax,
                                    'currency_code' => config('paypal.currency'),
                                ]
                            ]
                        ],
                        'items' => $items,
                    ]
                ],
            ]);


            dd($paypalOrder);
            DB::commit();

            return response()->json([]);
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


            DB::commit();

            return response()->json([]);
        } catch (\Exception $exception) {
            DB::rollBack();
            logs()->error($exception);
            return response()->json(['error' => $exception->getMessage()], 422);
        }
    }
}
