<?php

namespace Database\Seeders;

use App\Models\Permission_Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
    $permission_role =
    [
        //////////////////////////////////////////
        //Admin
        /////////////////////////////////////////
        [
            'role_id' => 1,
            'permission_id'=>1
        ],
        [
            'role_id' => 1,
            'permission_id'=>3
        ],
        [
            'role_id' => 1,
            'permission_id'=>4
        ],
        [
            'role_id' => 1,
            'permission_id'=>5
        ],
        [
            'role_id' => 1,
            'permission_id'=>7
        ],
        [
            'role_id' => 1,
            'permission_id'=>26
        ],
        [
            'role_id' => 1,
            'permission_id'=>27
        ],
        [
            'role_id' => 1,
            'permission_id'=>28
        ],
        [
            'role_id' => 1,
            'permission_id'=>29
        ],
        [
            'role_id' => 1,
            'permission_id'=>30
        ],
        [
            'role_id' => 1,
            'permission_id'=>31
        ],
        [
            'role_id' => 1,
            'permission_id'=>32
        ],
        [
            'role_id' => 1,
            'permission_id'=>33
        ],
        [
            'role_id' => 1,
            'permission_id'=>34
        ],
        [
            'role_id' => 1,
            'permission_id'=>35
        ],
        [
            'role_id' => 1,
            'permission_id'=>36
        ],
        [
            'role_id' => 1,
            'permission_id'=>40
        ],
        [
            'role_id' => 1,
            'permission_id'=>41
        ],
        [
            'role_id' => 1,
            'permission_id'=>42
        ],
        [
            'role_id' => 1,
            'permission_id'=>43
        ],

        ////////////////////////////////
        //Partner
        ////////////////////////////////
        [
            'role_id' => 2,
            'permission_id'=>2 /////////////////
        ],
        [
            'role_id' => 2,
            'permission_id'=>4 /////////////////
        ],
        [
            'role_id' => 2,
            'permission_id'=>5 /////////////////
        ],
        [
            'role_id' => 2,
            'permission_id'=>7 /////////////////
        ],
        [
            'role_id' => 2,
            'permission_id'=>8
        ],
        [
            'role_id' => 2,
            'permission_id'=>9
        ],
        /*[
            'role_id' => 2,
            'permission_id'=>10
        ],*/
        [
            'role_id' => 2,
            'permission_id'=>32
        ],
        [
            'role_id' => 2,
            'permission_id'=>40
        ],
        [
            'role_id' => 2,
            'permission_id'=>41
        ],
        [
            'role_id' => 2,
            'permission_id'=>42
        ],
        ////////////////////////////////
        //Cashier
        ////////////////////////////////
        [
            'role_id' => 3,
            'permission_id'=>4/////////////////
        ],
        [
            'role_id' => 3,
            'permission_id'=>5/////////////////
        ],
        [
            'role_id' => 3,
            'permission_id'=>7/////////////////
        ],
        [
            'role_id' => 3,
            'permission_id'=>41/////////////////
        ],

        ////////////////////////////////
        //Client
        ////////////////////////////////
        [
            'role_id' => 4,
            'permission_id'=>4
        ],
        [
            'role_id' => 4,
            'permission_id'=>5///////////////////////
        ],
        [
           'role_id' => 4,
            'permission_id'=>7///////////////////////
        ],
        [
            'role_id' => 4,
            'permission_id'=>22
        ],
        [
            'role_id' => 4,
            'permission_id'=>23
        ],
        [
            'role_id' => 4,
            'permission_id'=>24
        ],
        [
            'role_id' => 4,
            'permission_id'=>25
        ],
        [
            'role_id' => 4,
            'permission_id'=>40
        ],
        [
            'role_id' => 4,
            'permission_id'=>41
        ],

        ////////////////////////////////
        //Charity
        ////////////////////////////////
        [
            'role_id' => 5,
            'permission_id'=>4
        ],
        [
            'role_id' => 5,
            'permission_id'=>5//////////////////
        ],
        [
            'role_id' => 5,
            'permission_id'=>7//////////////////
        ],
        [
            'role_id' => 5,
            'permission_id'=>40//////////////////
        ],
        [
            'role_id' => 5,
            'permission_id'=>41//////////////////
        ],

        ////////////////////////////////
        //Admin wiht client and Charity
        ///////////////////////////////
        [
            'role_id' => 1,
            'permission_id'=>37
        ],
        [
            'role_id' => 1,
            'permission_id'=>38
        ],
        [
            'role_id' => 1,
            'permission_id'=>39
        ],
        ////////////////////////////////
        //Client wiht Admin and Charity
        ///////////////////////////////
        [
            'role_id' => 4,
            'permission_id'=>37
        ],
        [
            'role_id' => 4,
            'permission_id'=>38
        ],
        [
            'role_id' => 4,
            'permission_id'=>39
        ],

        ////////////////////////////////
        //Charity wiht client and Admin
        ///////////////////////////////
        [
            'role_id' => 5,
            'permission_id'=>37
        ],
        [
            'role_id' => 5,
            'permission_id'=>38
        ],
        [
            'role_id' => 5,
            'permission_id'=>39
        ],


        //////////////////////////////////
        //Partner with cashier
        /////////////////////////////////
        [
            'role_id' => 2,
            'permission_id'=>11
        ],
        [
            'role_id' => 2,
            'permission_id'=>12
        ],
        [
            'role_id' => 2,
            'permission_id'=>13
        ],
        [
            'role_id' => 2,
            'permission_id'=>14
        ],
        [
            'role_id' => 2,
            'permission_id'=>15
        ],
        [
            'role_id' => 2,
            'permission_id'=>16
        ],

        //////////////////////////////////
        //Cashier with partner
        /////////////////////////////////
        [
            'role_id' => 3,
            'permission_id'=>11
        ],
        [
            'role_id' => 3,
            'permission_id'=>12
        ],
        [
            'role_id' => 3,
            'permission_id'=>13
        ],
        [
            'role_id' => 3,
            'permission_id'=>14
        ],
        [
            'role_id' => 3,
            'permission_id'=>15
        ],
        [
            'role_id' => 3,
            'permission_id'=>16
        ],

        //////////////////////////////////
        //Client with charity
        /////////////////////////////////
        [
            'role_id' => 4,
            'permission_id'=>17
        ],
        [
            'role_id' => 4,
            'permission_id'=>18
        ],
        [
            'role_id' => 4,
            'permission_id'=>19
        ],
        [
            'role_id' => 4,
            'permission_id'=>20
        ],
        [
            'role_id' => 4,
            'permission_id'=>21
        ],
        [////////////////new
            'role_id' => 4,
            'permission_id'=>44
        ],
        [///////////////////new
            'role_id' => 4,
            'permission_id'=>45
        ],

        //////////////////////////////////
        //Charity with client
        /////////////////////////////////
        [
            'role_id' => 5,
            'permission_id'=>17
        ],
        [
            'role_id' => 5,
            'permission_id'=>18
        ],
        [
            'role_id' => 5,
            'permission_id'=>19
        ],
        [
            'role_id' => 5,
            'permission_id'=>20
        ],
        [
            'role_id' => 5,
            'permission_id'=>21
        ],
        [////////////////////new
            'role_id' => 5,
            'permission_id'=>44
        ],
        [/////////////////new
            'role_id' => 5,
            'permission_id'=>45
        ],
    ];
    Permission_Role::insert($permission_role);
    }
}
