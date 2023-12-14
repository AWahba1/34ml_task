<?php

namespace App\Listeners;

use App\Events\ProductOutOfStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Mail\ProductOutOfStockMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\OutOfStockNotification;


class SendOutOfStockNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProductOutOfStock $event): void
    {
        $product = $event->product;
        $adminEmail = env('ADMIN_EMAIL_ADDRESS', 'admin@34ml.com');

        Mail::to($adminEmail)->send(new OutOfStockNotification($product));
    }
}
