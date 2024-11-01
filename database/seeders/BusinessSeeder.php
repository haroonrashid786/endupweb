<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $array = ["Automotive", "Business Support & Supplies", "Computers & Electronics", "Construction & Contractors", "Education", "Entertainment", "Food & Dining", "Health & Medicine", "Home & Garden", "Legal & Financial", "Manufacturing, Wholesale,", "Distribution", "Miscellaneous", "Personal Care & Services", "Real Estate", "Travel & Transportation"];

        foreach ($array as $a){
            BusinessType::create([
                'name' => $a,
            ]);
        }
    }
}
