<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Contract\InvoicesServiceContract;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Order $order)
    {
        $this->authorize('view', $order);

        return app(InvoicesServiceContract::class)->generate($order)->stream();
    }
}
