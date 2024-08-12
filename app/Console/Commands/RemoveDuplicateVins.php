<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Illuminate\Console\Command;

class RemoveDuplicateVins extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RemoveDuplicateVins';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes duplicate vins';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $allVins = Vehicle::whereRaw('
        vin in (
          SELECT vin from vehicles
          GROUP by vehicles.vin
          having count(vehicles.vin)>1)
        ')
            ->get()
            ->groupBy('vin');

        $allVins->each(function ($vinBundle, $vin) {
            $hasKeys = $vinBundle->filter(function ($obj) {
                return !empty($obj->release_id) || !empty($obj->certificate_id);
            });

            $vehicleIds = $hasKeys->pluck('id')->implode(',');

            if (count($hasKeys) > 0) {
                dump("delete from vehicles where vin = '$vin' and id not in ($vehicleIds)");
            } else {
                $vehicleIds = $vinBundle->take(count($vinBundle) - 1)->pluck('id')->implode(',');
                dump("DELETE from vehicles where vin = '$vin' and id in ($vehicleIds)");
            }
        });
    }
}
