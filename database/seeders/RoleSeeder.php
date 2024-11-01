<?php

namespace Database\Seeders;

use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        Roles::truncate();

        $role1 = new Roles();
        $role1->name = 'Rider';
        $role1->unique_number = uniqid();
        $role1->save();

        $role2 = new Roles();
        $role2->name = 'Admin';
        $role2->unique_number = uniqid();
        $role2->save();

        $role3 = new Roles();
        $role3->name = 'Retailer';
        $role3->unique_number = uniqid();
        $role3->save();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
