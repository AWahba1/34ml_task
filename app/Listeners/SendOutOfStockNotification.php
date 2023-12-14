<?php

namespace App\Listeners;

use App\Events\ProductOutOfStock;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Mail\ProductOutOfStockMail;
use Illuminate\Support\Facades\Mail;

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

        Mail::raw("The {$product->title} product is now out of stock.", function ($message) use ($adminEmail) {
            $message->to($adminEmail)
                    ->subject('Product Out of Stock Notification');
        });
    }
}
