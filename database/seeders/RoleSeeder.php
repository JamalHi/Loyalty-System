<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles =
        [
            [
                'role' => 'admin',
            ],
            [
                'role' => 'partner'
            ],
            [
                'role' => 'cashier'
            ],
            [
                'role' => 'client'
            ],
            [
                'role' => 'charity'
            ],
        ];
        Role::insert($roles);
    }
}
