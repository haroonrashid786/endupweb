<?php

namespace Database\Seeders;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usercheck = User::where('email', 'dev@hashedsystem.com')->first();
        if ($usercheck != null){
            $usercheck->delete();
        }
        $user = new User();
        $user->name = 'Admin';
        $user->email = 'dev@hashedsystem.com';
        $user->mobile = '03242841822';
        $user->username = 'admin';
        $user->save();
        $user->password()->create([
            'password' => Hash::make('123456')
        ]);

        $role = Roles::where('name', 'Admin')->first();
        $user->roles()->attach($role->id);
    }
}
