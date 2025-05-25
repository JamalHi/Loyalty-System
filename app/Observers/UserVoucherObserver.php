<?php

namespace App\Observers;

use App\Models\User_Voucher;
use Illuminate\Support\Facades\Cache;

class UserVoucherObserver
{
    /**
     * Handle the User_Voucher "created" event.
     */
    public function created(User_Voucher $user_Voucher): void
    {
        Cache::forget('UserVouchers');
    }

    /**
     * Handle the User_Voucher "updated" event.
     */
    public function updated(User_Voucher $user_Voucher): void
    {
        Cache::forget('UserVouchers');
    }

    /**
     * Handle the User_Voucher "deleted" event.
     */
    public function deleted(User_Voucher $user_Voucher): void
    {
        Cache::forget('UserVouchers');
    }

    /**
     * Handle the User_Voucher "restored" event.
     */
    public function restored(User_Voucher $user_Voucher): void
    {
        //
    }

    /**
     * Handle the User_Voucher "force deleted" event.
     */
    public function forceDeleted(User_Voucher $user_Voucher): void
    {
        //
    }
}
