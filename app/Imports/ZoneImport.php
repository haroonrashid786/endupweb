<?php

namespace App\Imports;

use App\Models\PostalCode;
use App\Models\Zone;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithUpserts;

class ZoneImport implements ToCollection, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function collection(collection $row)
    {
        // ini_set('memory_limit', '256M');
        // set_time_limit(1200);
        $chunkSize = 20;
        $row->chunk($chunkSize)->each(function ($chunk) {
            $insertData = [];
            foreach ($chunk as $row) {
                $data = $row->toArray();

                foreach ($data as $dkey => $dval) {
                    $zone = strtoupper(str_replace('_', ' ', $dkey));

                    $postal = $dval;
                    // dd($postal);
                    $zoneId = Zone::where('name', $zone)->first();
                    if (!is_numeric($zone)) {


                        if (!is_null($zoneId)) {
                            $insertData[] = [
                                'postal' => $postal,
                                'zone_id' => $zoneId->id,
                            ];
                        } else {
                            $zoneId = Zone::create([
                                'name' => $zone,
                                'active' => 1,
                            ]);

                            if (!empty($postal)) {
                                $insertData[] = [
                                    'postal' => $postal,
                                    'zone_id' => $zoneId->id,
                                ];
                            }
                        }
                    }
                }
            }
            PostalCode::insert($insertData);
        });
        // Logic to create and return a new model instance based on the data in $row

        // $existingZones = Zone::pluck('id', 'name');
        // $dataToInsert = [];
        // foreach ($row->toArray() as $d) {

        //     foreach ($d as $dkey => $dval) {
        //         $zoneName = strtoupper(str_replace('_', ' ', $dkey));
        //         $postal = $dval;

        //         $zoneId = $existingZones[$zoneName] ?? Zone::create(['name' => $zoneName, 'active' => 1])->id;

        //         if (!empty($postal)) {
        //             $dataToInsert[] = ['zone_id' => $zoneId, 'postal' => $postal];
        //         }

        //     }
        //     sleep(10);
        // }
        // dd($dataToInsert);
        // PostalCode::upsert($dataToInsert, ['postal'], ['zone_id', 'postal']);
    }
}
