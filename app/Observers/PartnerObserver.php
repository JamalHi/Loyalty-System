<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;

class PartnerObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if($user->role_id == 2)
        {
            Cache::forget('Partners');
        }
    }

        /**
     * Handle the Voucher "updated" event.
     */
    public function updated(User $user): void
    {
        if($user->role_id == 2)
        {
            Cache::forget('Partners');
        }
    }

    /**
     * Handle the Voucher "deleted" event.
     */
    public function deleted(User $user): void
    {
        if($user->role_id == 2)
        {
            Cache::forget('Partners');
        }
    }

}
