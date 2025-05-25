<?php

namespace App\Observers;

use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class VoucherObserver
{
    /**
     * Handle the Voucher "created" event.
     */
    public function created(Voucher $voucher): void
    {
        Cache::forget('Vouchers');
    }

    /**
     * Handle the Voucher "updated" event.
     */
    public function updated(Voucher $voucher): void
    {
        Cache::forget('Vouchers');
        //if(Auth::id() == $voucher->user_id) { Cache::forget('MyAcceptedVouchers'); }
    }

    /**
     * Handle the Voucher "deleted" event.
     */
    public function deleted(Voucher $voucher): void
    {
        Cache::forget('Vouchers');
        //if(Auth::id() == $voucher->user_id) { Cache::forget('MyAcceptedVouchers'); }
    }

    /**
     * Handle the Voucher "restored" event.
     */
    public function restored(Voucher $voucher): void
    {
        //
    }

    /**
     * Handle the Voucher "force deleted" event.
     */
    public function forceDeleted(Voucher $voucher): void
    {
        //
    }
}
