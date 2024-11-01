<?php

namespace Database\Seeders;

use App\Models\WorkingDay;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestEnvironmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $new = new WorkingDay();
        $new->day = 'Monday';
        $new->save();

        $new2 = new WorkingDay();
        $new2->day = 'Tuesday';
        $new2->save();

        $z = new Zone();
        $z->name = 'Walton';
        $z->code = 'WT';
        $z->save();

    }
}
