<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class ThankYouController extends Controller
{

    public function __invoke(string $vendorOrderId)
    {
        try {
            $order = Order::with(['transaction', 'products', 'status'])
                ->where('vendor_order_id', $vendorOrderId)
                ->firstOrFail();

            return view('orders/thank-you', compact('order'));
        } catch (\Exception $exception) {
            return redirect()->route('home');
        }
    }
}
