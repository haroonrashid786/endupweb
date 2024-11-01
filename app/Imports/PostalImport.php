<?php

namespace App\Imports;

use App\Models\PostalCode;
use App\Models\Zone;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PostalImport implements ToCollection
{
    /**
     * @param Collection $collection
     */

    protected $zoneID;

    public function __construct($zoneID)
    {
        $this->zoneID = $zoneID;
    }

    public function collection(Collection $collection)
    {
        $zone = Zone::find($this->zoneID);


        $chunkSize = 100; // Control Chunk size from here.
        $collection->chunk($chunkSize)->each(function ($chunk) use ($zone) {
            $data = [];
            foreach ($chunk as $key => $c) {

                // Generating an array to post data in database
                if (isset($c[0])) {
                    $data[] = [
                        'postal' => $c[0],
                        'zone_id' => $zone->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

            }

            // Inserting or updating the data in the database
            foreach ($data as $record) {
                PostalCode::updateOrInsert(
                    ['postal' => $record['postal'], 'zone_id' => $record['zone_id']],
                    $record
                );
            }
            // sleep(1); // Script/Code will sleep for 1second after each chunk operation
        });
    }
}
