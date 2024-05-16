<?php

namespace App\Http\Controllers\Ajax\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\CreateOrderRequest;
use App\Services\Payments\Contract\PaypalServiceContract;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\JsonResponse;

class PaypalController extends Controller
{
    public function __construct(protected PaypalServiceContract $paypal)
    {
    }

    public function create(CreateOrderRequest $request): JsonResponse
    {
        return $this->paypal->create($request);
    }

    public function capture(string $vendorOrderId): JsonResponse
    {
        $response = $this->paypal->capture($vendorOrderId);

        Cart::instance('cart')->destroy();

        return $response;
    }
}
