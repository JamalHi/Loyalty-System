<?php

namespace App\Providers;

use App\Models\Charity;
use App\Models\Offer;
use App\Models\Partner;
use App\Models\User;
use App\Models\Voucher;
use App\Models\User_Voucher;
use App\Observers\OfferObserver;
use App\Observers\VoucherObserver;
use App\Observers\UserVoucherObserver;
use App\Observers\PartnerObserver;
use App\Observers\CharityObserver;
use App\Observers\MyAcceptedVouchersObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Offer::observe(OfferObserver::class);
        Voucher::observe(VoucherObserver::class);
        User::observe(PartnerObserver::class);
        User_Voucher::observe(UserVoucherObserver::class);
        Charity::observe(CharityObserver::class);
        Voucher::observe(MyAcceptedVouchersObserver::class);
    }
}
