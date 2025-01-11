<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class ShipOrder implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        $shippingUrl = sprintf('%s/api/v1/orders/%s/shipping', config('constants.shipping_service_base_url'), $this->order->id);

        // make api request to service 2 ship endpoint
        Http::throw()->post($shippingUrl);
    }
}
