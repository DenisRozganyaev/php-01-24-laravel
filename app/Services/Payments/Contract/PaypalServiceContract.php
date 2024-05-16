<?php

namespace App\Services\Payments\Contract;

use App\Http\Requests\Ajax\CreateOrderRequest;
use Illuminate\Http\JsonResponse;

interface PaypalServiceContract
{
    public function create(CreateOrderRequest $request): JsonResponse;

    public function capture(string $vendorOrderId): JsonResponse;
}
