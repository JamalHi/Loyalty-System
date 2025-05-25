<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission =
        [
            [
                'name' => 'createAccountPartner',//1
            ],
            [
                'name' => 'createAccountCashier',//2    // partner
            ],
            [
                'name' => 'createAccountCharity',//3
            ],
            [
                'name' => 'update_account',//4  //all
            ],
            [
                'name' => 'show_profile',//5  //all
            ],

            //////////////////////All//////////////////////////////////
            [
                'name' => 'generate_otp',//6
            ],
            [
                'name' => 'email_verification',//7
            ],

            /////////////////// Partner //////////////////////////
            [
                'name' => 'add_offer',//8
            ],
            [
                'name' => 'add_voucher_request',//9
            ],
            [
                'name' => 'show_points_history',//10
            ],

            //////////////////////Partner  And Cashier////////////////////////////

            [
                'name' => 'show_my_offers',//11
            ],
            [
                'name' => 'show_my_accept_voucher',//12
            ],
            [
                'name' => 'calculate_points_from_invoice',//13
            ],
            [
                'name' => 'add_points_to_client',//14
            ],
            [
                'name' => 'consume_voucher',//15
            ],
            [
                'name' => 'consume_offer',//16
            ],

            /////////////////Charity And Client ////////////////////

            [
                'name' => 'Charity_Client_generate_otp',//17
            ],
            [
                'name' => 'show_partners',//18
            ],
            [
                'name' => 'buy_voucher',//19
            ],
            [
                'name' => 'use_voucher',//20
            ],
            [
                'name' => 'show_my_bought_voucher',//21
            ],

            ///////////////          Client       /////////////////

            [
                'name' => 'show_all_charities',//22
            ],
            [
                'name' => 'watch_ad',//23
            ],
            [
                'name' => 'donate_special_points',//24
            ],
            [
                'name' => 'transfer_point_to_friend',//25
            ],

            /////////////////////Admin////////////////////

            [
                'name' => 'show_users',//26
            ],
            [
                'name' => 'user_search',//27
            ],
            [
                'name' => 'delete_user',//28
            ],
            [
                'name' => 'block_user',//29
            ],
            [
                'name' => 'show_vouchers_request',//30
            ],
            [
                'name' => 'accept_deny_voucher_request',//31
            ],
            [
                'name' => 'delete_offer',//32
            ],
            [
                'name' => 'edit_service',//33
            ],
            [
                'name' => 'add_advertisement',//34
            ],
            [
                'name' => 'show_all_ads',//35
            ],
            [
                'name' => 'update_ad_status',//36
            ],

            //////////////////Client  Admin  Charity////////////////////////
            [
                'name' => 'show_all_offers',//37
            ],
            [
                'name' => 'show_all_vouchers',//38
            ],
            [
                'name' => 'show_partner_details',//39
            ],

            ///////////////////All //////////////////////////////

            [
                'name' => 'show_points_history',//40
            ],
            [
                'name' => 'logout',//41
            ],

            ///////////////////NEW ////////////////////////////
            [
                'name' => 'delete_voucher',//42 // Admin And Partner
            ],
            [
                'name' => 'add_points_to_partner',//43  Admin
            ],

            /////////////// new ///////////////////
            [
                'name' => 'show_partner_offers'//44
            ],
            [
                'name' => 'show_partner_vouchers'//45
            ],

        ];
        Permission::insert($permission);
    }
}
