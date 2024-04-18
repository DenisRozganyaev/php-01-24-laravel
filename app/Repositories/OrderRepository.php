<?php

namespace App\Repositories;


use App\Enums\PaymentSystem;
use App\Enums\TransactionStatus;
use App\Models\Order;
use App\Models\OrderStatus;
use Gloudemans\Shoppingcart\Facades\Cart;

class OrderRepository implements Contract\OrderRepositoryContract
{
    public function create(array $data): false|Order
    {
        $status = OrderStatus::default()->first();
        $data = array_merge($data, ['status_id' => $status->id]);

        $order = auth()->check()
            ? auth()->user()->orders()->create($data)
            : Order::create($data);

        $this->addProductsToOrder($order);

        return $order;
    }

    public function setTransaction(string $vendorOrderId, PaymentSystem $system, TransactionStatus $status): Order
    {
        return new Order();
    }

    protected function addProductsToOrder(Order $order): void
    {
        Cart::instance('cart')->content()->each(function ($item) use ($order) {
            $product = $item->model;

            $order->products()->attach($product, [
                'quantity' => $item->qty,
                'single_price' => $item->price,
                'name' => $product->title,
            ]);

            $quantity = $product->quantity - $item->qty;

            if (! $product->update(['quantity' => $quantity])) {
                throw new \Exception("Smth went wring with quantity update on product [id: $product->id]");
            }
        });
    }
}
