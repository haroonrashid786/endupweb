<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use App\Models\OrderType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = ["Document", "Parcel", "Corrugated Bag",  "Rigid Box",   "Poly Bags", "Plastic Box", "Paperboard Box"];
        foreach ($array as $a) {
            OrderType::create([
                'name' => $a,
            ]);
        }
    }
}
