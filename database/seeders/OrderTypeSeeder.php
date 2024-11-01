<?php

namespace Database\Seeders;

use App\Models\OrderType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;


class OrderTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = ["Document", "Parcel", "1 KG Pack", "2 KG Pack", "5 KG Pack", "10 KG Pack","Others"];

        foreach ($array as $a) {
            OrderType::create([
                'name' => $a,
            ]);
        }
    }
}
