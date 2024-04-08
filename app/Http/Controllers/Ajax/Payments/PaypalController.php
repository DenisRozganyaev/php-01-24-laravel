<?php

namespace App\Http\Controllers\Ajax\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ajax\CreateOrderRequest;
use App\Services\Payments\Contract\PaypalServiceContract;
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
        return $this->paypal->capture($vendorOrderId);
    }
}
