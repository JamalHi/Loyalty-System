<?php

namespace App\Observers;

use App\Models\Voucher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Partner;
use App\Models\Cashier;

class MyAcceptedVouchersObserver
{
    /**
     * Handle the Voucher "created" event.
     */
    public function created(Voucher $voucher): void
    {
    }

    /**
     * Handle the Voucher "updated" event.
     */
    public function updated(Voucher $voucher): void
    {
        Cache::forget('MyAcceptedVouchers');
    //    $person = User::where("id",$voucher->user_id)->first();

    //    echo $person;
    //    if($person->role_id == 2)
    //    {

    //     $partner_user = $person->id;
    //     if($partner_user->id == $voucher->user_id) {  Cache::forget('MyAcceptedVouchers'); }
    //    }
    //    else if ($person->role_id == 3)
    //    {
    //        $cashier = Cashier::where('user_id',$person->id)->first();
    //        $partner_user = User::where('id',$cashier->partner_id)->first();
    //        if($partner_user->id == $voucher->user_id) { Cache::forget('MyAcceptedVouchers'); }
    //    }


    }

    /**
     * Handle the Voucher "deleted" event.
     */
    public function deleted(Voucher $voucher): void
    {
        Cache::forget('MyAcceptedVouchers');

        // $person = User::where("id",Auth::id())->first();

        // if($person->role_id == 2)
        // {
        //  $partner = Partner::where('user_id',Auth::id())->first();
        //  $partner_user = User::where('id',$partner->user_id)->first();
        // }
        // else if ($person->role_id == 3)
        // {
        //     $cashier = Cashier::where('user_id',$person->id)->first();
        //     $partner_user = User::where('id',$cashier->partner_id)->first();
        // }

        // if($partner_user->id == $voucher->user_id) { Cache::forget('MyAcceptedVouchers'); }
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
