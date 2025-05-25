<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth_controller;
use App\Http\Controllers\admin_controller;
use App\Http\Controllers\charts_controller;
use App\Http\Controllers\client_controller;
use App\Http\Controllers\partner_controller;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\admin_partner_controller;
use App\Http\Controllers\charity_client_controller;
use App\Http\Controllers\partner_cashier_controller;


use App\Http\Controllers\admin_client_charity_controller;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/sign-up/admin',              [auth_controller::class,'createAccountAdmin'])           ->name('createAccountAdmin');
Route::post('auth/sign-up/client',             [auth_controller::class,'createAccountClient'])          ->name('createAccountClient');
Route::post('reset_password',                  [auth_controller::class,'reset_password'])               ->name('reset_password');
Route::post('generate_otp_with_email',         [auth_controller::class,'generate_otp_with_email'])      ->name('generate_otp_with_email');
Route::post('otp_verification',                [auth_controller::class,'otp_verification'])             ->name('otp_verification');

Route::middleware(['active.status'])->group(function(){
    Route::post('auth/login',                  [auth_controller::class,'login'])                        ->name('login');
});
Route::middleware(['auth:api','active.status'])->group(function(){

    //////////////////////////////////  admin  ////////////////////
    Route::middleware(['access.admin' ])->group(function(){
        //auth controller
        Route::post('auth/sign-up/partner',            [auth_controller::class,'createAccountPartner'])         ->name('createAccountPartner');
        Route::post('auth/add/partner/image/{id}',     [auth_controller::class,'add_parnter_image'])            ->name('add_parnter_image');//admin
        Route::post('auth/sign-up/charity',            [auth_controller::class,'createAccountCharity'])         ->name('createAccountCharity');
    });

    //////////////////////////////////  admin + parnter + cashier + client + charity ////////////////////

    Route::middleware(['access.all_roles'])->group(function(){

        Route::post('update_account',                    [auth_controller::class,'update_account'])                               ->name('update_account');
        Route::get('show_profile/{id}',                  [auth_controller::class,'show_profile'])                                 ->name('show_profile');
        Route::post('auth/logout',                       [auth_controller::class,'logout'])                                       ->name('logout');
        Route::get('show_points_history',                [charity_client_controller::class,'show_points_history'])                ->name('show_points_history');
        Route::get('show_points_history_partner_cashier',[charity_client_controller::class,'show_points_history_partner_cashier'])->name('show_points_history_partner_cashier'); });
        Route::post('chart', [charts_controller::class, 'chart']);
        Route::get('user_count', [charts_controller::class, 'user_count']);
        //////////////////////////////////  partner  ////////////////////

    Route::middleware(['access.partner' ])->group(function(){

        Route::post('auth/sign-up/cashier',            [auth_controller::class,'createAccountCashier'])         ->name('createAccountCashier');
        Route::post('auth/partner_password',           [auth_controller::class,'partner_password'])             ->name('partner_password');
        Route::post('add_offer',                       [partner_controller::class,'add_offer'])                 ->name('add_offer');
        Route::post('add_offer_images',                [partner_controller::class,'add_offer_images'])          ->name('add_offer_images');//parnter
        Route::post('add_voucher_request',             [partner_controller::class,'add_voucher_request'])       ->name('add_voucher_request');
        Route::get('show_my_cashier/{partner_id}',     [partner_controller::class,'show_my_cashier'])           ->name('show_my_cashier');
        Route::delete('delete_cashier/{id}',           [partner_controller::class,'delete_cashier'])            ->name('delete_cashier');
    });

        //////////////////////////////////  partner + cashier  ////////////////////

    Route::middleware(['access.partner_cashier'])->group(function(){

        Route::get('show_my_offers',                   [partner_cashier_controller::class,'show_my_offers'])               ->name('show_my_offers');
        Route::get('show_my_accept_voucher',           [partner_cashier_controller::class,'show_my_accept_voucher'])       ->name('show_my_accept_voucher');
        Route::post('information_client_from_otp',     [partner_cashier_controller::class,'information_client_from_otp'])  ->name('information_client_from_otp');
        Route::post('calculate_points_from_invoice',   [partner_cashier_controller::class,'calculate_points_from_invoice'])->name('calculate_points_from_invoice');
        Route::post('add_points_to_client',            [partner_cashier_controller::class,'add_points_to_client']);
        Route::post('get_voucher_info_from_otp',       [partner_cashier_controller::class,'get_voucher_info_from_otp']);
        Route::post('consume_voucher',                 [partner_cashier_controller::class,'consume_voucher'])              ->name('consume_voucher');
        Route::post('consume_offer',                   [partner_cashier_controller::class,'consume_offer'])                ->name('consume_offer');
        Route::post('get_my_vouchers_of_one_client',   [partner_cashier_controller::class,'get_my_vouchers_of_one_client']);
    });

        //////////////////////////////////  Charity + Client  ////////////////////

    Route::middleware(['access.charity_client'])->group(function(){

        Route::post('Charity_Client_generate_otp',     [charity_client_controller::class,'generate_otp'])                 ->name('Charity_Client_generate_otp');
        Route::get('show_partners',                    [charity_client_controller::class,'show_partners'])                ->name('show_partners');
        Route::post('buy_voucher/{id}',                [charity_client_controller::class,'buy_voucher'])                  ->name('buy_voucher');
        Route::post('use_voucher/{id}',                [charity_client_controller::class,'use_voucher'])                  ->name('use_voucher');
        Route::get('show_my_bought_voucher',           [charity_client_controller::class,'show_my_bought_voucher'])       ->name('show_my_bought_voucher');
        Route::get('show_partner_offers/{id}',         [charity_client_controller::class,'show_partner_offers'])          ->name('show_partner_offers');
        Route::get('show_partner_vouchers/{id}',       [charity_client_controller::class,'show_partner_vouchers'])        ->name('show_partner_vouchers');
    });

        //////////////////////////////////  Client  ////////////////////

    Route::middleware(['access.client'])->group(function(){

        Route::get('show_all_charities',               [client_controller::class,'show_all_charities'])           ->name('show_all_charities');
        Route::get('watch_ad',                         [client_controller::class,'watch_ad'])                     ->name('watch_ad');
        Route::get('get_points_from_ad/{user_id}/{ad_id}',[client_controller::class,'get_points_from_ad'])        ->name('get_points_from_ad');
        Route::post('donate_special_points',           [client_controller::class,'donate_special_points'])        ->name('donate_special_points');
        Route::post('transfer_point_to_friend',        [client_controller::class,'transfer_point_to_friend'])     ->name('transfer_point_to_friend');
    });

        //////////////////////////////////  Admin  ////////////////////

    Route::middleware(['access.admin'])->group(function(){

        Route::post('show_users',                      [admin_controller::class,'show_users'])                   ->name('show_users');
        Route::post('user_search',                     [admin_controller::class,'user_search'])                  ->name('user_search');
        Route::delete('delete_user/{id}',              [admin_controller::class,'delete_user'])                  ->name('delete_user');
        Route::get('block_user/{id}',                  [admin_controller::class,'block_user'])                   ->name('block_user');
        Route::get('show_vouchers_request',            [admin_controller::class,'show_vouchers_request'])        ->name('show_vouchers_request');
        Route::post('accept_deny_voucher_request/{id}',[admin_controller::class,'accept_deny_voucher_request'])  ->name('accept_deny_voucher_request');
        Route::post('edit_service',                    [admin_controller::class,'edit_service'])                 ->name('edit_service');
        Route::post('add_advertisement',               [admin_controller::class,'add_advertisement'])            ->name('add_advertisement');
        Route::get('show_all_ads',                     [admin_controller::class,'show_all_ads'])                 ->name('show_all_ads');
        Route::post('update_ad_status/{id}',           [admin_controller::class,'update_ad_status'])             ->name('update_ad_status');
        Route::post('add_points_to_partner',           [admin_controller::class,'add_points_to_partner'])        ->name('add_points_to_partner');
        Route::get('show_client_vouchers/{id}',        [admin_controller::class,'show_client_vouchers'])         ->name('show_client_vouchers');//admin
        Route::get('show_sevices',                     [admin_controller::class,'show_sevices'])                 ->name('show_sevices');//admin
    });

        //////////////////////////////////  Admin + Client + charity  ////////////////////

    Route::middleware(['access.client_admin_charity'])->group(function(){
    //Route::group(['middleware' => ['access.client', 'access.admin', 'access.charity']], function() {
        Route::get('show_all_offers',                  [admin_client_charity_controller::class,'show_all_offers'])              ->name('show_all_offers');
        Route::get('show_all_vouchers',                [admin_client_charity_controller::class,'show_all_vouchers'])            ->name('show_all_vouchers');
        Route::get('show_partner_details/{id}',        [admin_client_charity_controller::class,'show_partner_details'])         ->name('show_partner_details');
        Route::get('most_bought_vouchers',             [admin_client_charity_controller::class,'most_bought_vouchers'])               ->name('most_bought_vouchers');//super new
    });

        //////////////////////////////////  Admin + partner  ////////////////////

    Route::middleware(['access.admin_partner'])->group(function(){

        Route::delete('delete_offer/{id}',                [admin_partner_controller::class,'delete_offer'])              ->name('delete_offer');
        Route::delete('delete_voucher/{id}',              [admin_partner_controller::class,'delete_voucher'])            ->name('delete_voucher');
    });
});

Route::middleware(['auth:api'])->group(function(){

    Route::get('generate_otp',       [auth_controller::class,'generate_otp'])            ->name('generate_otp');
    Route::post('email_verification',[auth_controller::class,'email_verification'])      ->name('email_verification');
    Route::post('update_email',      [auth_controller::class,'update_email'])            ->name('update_email');

});

Route::middleware(['auth:api','active.status'])->group(function(){

    Route::post('add_device_key',    [NotificationController::class, 'add_device_key']);
    Route::get('get_notify',         [NotificationController::class, 'get_notify']);
});

Route::get('test',[auth_controller::class,'test'])->name('test');
Route::post('send-notification', [NotificationController::class, 'send']);


Route::post('chart_test', [charts_controller::class, 'test']);

//Route::post('chart', [charts_controller::class, 'chart']);
//Route::get('user_count', [charts_controller::class, 'user_count']);
