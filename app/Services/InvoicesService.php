<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Invoice;

class InvoicesService implements Contract\InvoicesServiceContract
{

    public function generate(Order $order): Invoice
    {
        $order->loadMissing(['transaction', 'products', 'status']);

        $customer = new Buyer([
            'name' => $order->name . ' ' . $order->lastname,
            'phone' => $order->phone,
            'custom_fields' => [
                'email' => $order->email,
                'city' => $order->city,
                'address' => $order->address,
            ]
        ]);

        $fileName = Str::slug($customer->name . ' ' . $order->vendor_order_id);
        $invoice = Invoice::make('receipt')
            ->series('BIG')
            ->status($order->status->name->value)
            ->buyer($customer)
            ->date($order->created_at)
            ->filename($fileName)
            ->addItems($this->getInvoiceItems($order->products))
            ->logo(public_path('vendor/invoices/sample-logo.png'))
            ->taxRate(config('cart.tax'))
            ->save('public');

        if ($order->status->name->value === OrderStatusEnum::InProcess->value) {
            $invoice->payUntilDays(config('invoices.date.pay_until_days'));
        }

        return $invoice;
    }

    protected function getInvoiceItems(Collection $products): array
    {
        $items = [];

        foreach ($products as $product) {
            $items[] = InvoiceItem::make($product->title)
                ->pricePerUnit($product->pivot->single_price)
                ->quantity($product->pivot->quantity)
                ->units('шт');
        }

        return $items;
    }
}
