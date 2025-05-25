<?php

namespace App\Observers;

use App\Models\Offer;
use Illuminate\Support\Facades\Cache;

class OfferObserver
{
    /**
     * Handle the Offer "created" event.
     */
    public function created(Offer $offer): void
    {
        Cache::forget('Offers');
    }

    /**
     * Handle the Voucher "updated" event.
     */
    public function updated(Offer $offer): void
    {
        Cache::forget('Offers');
    }

    /**
     * Handle the Voucher "deleted" event.
     */
    public function deleted(Offer $offer): void
    {
        Cache::forget('Offers');
    }
}
