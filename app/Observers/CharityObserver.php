<?php

namespace App\Observers;

use App\Models\Charity;
use Illuminate\Support\Facades\Cache;

class CharityObserver
{
    /**
     * Handle the Charity "created" event.
     */
    public function created(Charity $charity): void
    {
        Cache::forget('Charities');
    }

    /**
     * Handle the Charity "updated" event.
     */
    public function updated(Charity $charity): void
    {
        Cache::forget('Charities');
    }

    /**
     * Handle the Charity "deleted" event.
     */
    public function deleted(Charity $charity): void
    {
        Cache::forget('Charities');
    }

    /**
     * Handle the Charity "restored" event.
     */
    public function restored(Charity $charity): void
    {
        //
    }

    /**
     * Handle the Charity "force deleted" event.
     */
    public function forceDeleted(Charity $charity): void
    {
        //
    }
}
