<?php

namespace App\Http\Controllers\Reports\Vehicles;

use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;

class VehicleReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:view_vehicles_report', ['only' => ['index', 'list']]);
        $this->middleware('permission:download_vehicles_report', ['only' => ['download']]);
    }

    public function index()
    {
        return view('reports.vehicles.vehicles.index');
    }

    public function list()
    {
        if (getFilterRule('releases.date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getVehiclesReportQuery()
            ->jsonJPaginate();

        $stats = $this->getVehiclesReportStats();

        $stats = [
            [
                'name' => 'Total Vehicle Count',
                'value' => $stats->total_vehicle_count,
                'group' => 'Statics',
            ],
            [
                'name' => 'Total Release Count',
                'value' => number_format($stats->total_release_count),
                'group' => 'Statics',
            ],
        ];

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    private function getVehiclesReportQuery()
    {
        $queryBuilder = JQueryBuilder::for(Vehicle::class)
            ->leftJoin('vehicle_models', 'vehicle_models.id', '=', 'vehicles.vehicle_model_id')
            ->leftJoin('vehicle_makes', 'vehicle_makes.id', '=', 'vehicle_models.vehicle_make_id')
            ->leftJoin('releases', 'vehicles.release_id', '=', 'releases.id')
            ->allowedFilters([
                AllowedFilter::custom('releases.date', new BetweenFilter),
                'vehicles.id',
                'vehicles.condition',
                'vehicles.vin',
                'vehicles.type',
                'vehicles.color',
                'vehicle_models.name',
                'vehicle_makes.name',
                'vehicles.coc_number',
                'vehicles.coc_date',
                'vehicles.coc_issuer',

                'releases.id',
                // 'releases.date',
                'releases.result',
                'releases.transit_number',
                'releases.inspector',
                'releases.office',

            ])
            ->selectRaw('
                releases.office as release_office,
                vehicles.id as vehicle_id,
                vehicles.condition as vehicle_condition,
                vehicles.vin as vehicle_vin,
                vehicles.type as vehicle_type,
                vehicles.color as vehicle_color,
                vehicle_models.name as vehicle_model_name,
                vehicle_makes.name as vehicle_make_name,
                vehicles.coc_number as vehicle_coc_number,
                vehicles.coc_date as vehicle_coc_date,
                vehicles.coc_issuer as vehicle_coc_issuer,

                releases.id as release_id,
                releases.date as release_date,
                IF(releases.result = 1, "Release", "Discrepancy") as release_result,
                releases.transit_number as release_transit_number,
                releases.inspector as release_inspector

                ')
            ->orderBy('releases.id', 'asc');

        return $queryBuilder;
    }

    public function download()
    {
        if (getFilterRule('releases.date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $vehicles = $this->getVehiclesReportQuery()->get();

        $this->createExcel($vehicles->toArray(), [
            'Release Office',
            'V. Id',
            'V. Condition',
            'Vin',
            'V. Type',
            'Color',
            'Make',
            'Model',
            'Coc #',
            'V. Coc Date',
            'V. Coc Issuer',
            'R. Id',
            'R. Date',
            'R. Result',
            'R. Transit #',
            'R. Inspector',
        ], 'VehicleReleasesReports' . now() . '.xlsx');
    }

    private function getVehiclesReportStats()
    {
        $vehiclesStats = $this->getVehiclesReportQuery()->get();

        return (object) [
            'total_vehicle_count' => $vehiclesStats->count(),
            'total_release_count' => $vehiclesStats->unique('release_id')->count(),
        ];
    }
}
