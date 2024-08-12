<?php

namespace App\Http\Controllers\Reports\Vehicles;

use App\Helpers\PledgeAmountStatusEnum;
use App\Helpers\StatsRowProducer;
use App\Http\Controllers\Reports\BaseReportController;
use App\Models\Pledge;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;
use App\Filters\BetweenFilter;
use Spatie\QueryBuilder\AllowedFilter;

class PledgeReportController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();

        $this->middleware('permission:view_pledges_report', ['only' => ['index', 'list']]);
        $this->middleware('permission:download_pledges_report', ['only' => ['download']]);
    }

    public function index()
    {
        return view('reports.vehicles.pledges.index');
    }

    public function list()
    {
        if (getFilterRule('pledges.pledge_date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $jsonJPaginate = $this->getQuery()
            ->jsonJPaginate();

        $stats = $this->getStats();

        return array_merge($jsonJPaginate, ['stat_rows' => $stats]);
    }

    private function getStats()
    {
        $stats = $this->getQuery()->get();

        return [
            StatsRowProducer::produce('Pledge', $stats->count(), 'Pledge Count'),
            StatsRowProducer::produce('Vins (Unique)', $stats->unique('vin')->count(), 'Pledge Count'),

            StatsRowProducer::produce('Pending Deposit', $stats->where('amount_status', PledgeAmountStatusEnum::PENDING_DEPOSIT)->sum('amount'), 'Pledge Amount (SUM)'),
            StatsRowProducer::produce('Deposited', $stats->where('amount_status', PledgeAmountStatusEnum::DEPOSITED)->sum('amount'), 'Pledge Amount (SUM)'),
            StatsRowProducer::produce('Refunded', $stats->where('amount_status', PledgeAmountStatusEnum::REFUNDED)->sum('amount'), 'Pledge Amount (SUM)'),
            StatsRowProducer::produce('Transfered', $stats->where('amount_status', PledgeAmountStatusEnum::TRANSFERED)->sum('amount'), 'Pledge Amount (SUM)'),
            StatsRowProducer::produce('Total', $stats->sum('amount'), 'Pledge Amount (SUM)'),

            StatsRowProducer::produce(
                'Not Received',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->where('pledge_payment_received_at', null)
                    ->count(),
                'Transfered Count'
            ),
            StatsRowProducer::produce(
                'Received',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->whereNotIn('pledge_payment_received_at', [null])
                    ->count(),
                'Transfered Count'
            ),

            StatsRowProducer::produce(
                'Revoked',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->whereNotIn('pledge_payment_revoked_on', [null])
                    ->count(),
                'Transfered Count'
            ),

            StatsRowProducer::produce(
                'Not Received',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->where('pledge_payment_received_at', null)
                    ->sum('pledge_payment_amount'),
                'Transfered (SUM)'
            ),
            StatsRowProducer::produce(
                'Received',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->whereNotIn('pledge_payment_received_at', [null])
                    ->sum('pledge_payment_amount'),
                'Transfered (SUM)'
            ),
            StatsRowProducer::produce(
                'Revoked',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->whereNotIn('pledge_payment_revoked_on', [null])
                    ->sum('pledge_payment_amount'),
                'Transfered (SUM)'
            ),
            StatsRowProducer::produce(
                'Total',
                $stats->whereNotIn('pledge_payment_amount_id', [null])
                    ->sum('pledge_payment_amount'),
                'Transfered (SUM)'
            ),
        ];
    }

    public function download(Request $request)
    {
        if (getFilterRule('pledges.pledge_date') == null) {
            return ezReturnErrorMessage('Select a date range');
        }

        $vehicles = $this->getQuery($request)->get();

        $this->createExcel($vehicles->toArray(), [
            'Pledge Id',
            'Pledge Date',
            'Remarks',
            'Amount',
            'Amount status',
            'Payment Amount',
            'Payment received by',
            'Payment received at',
            'Payment revoked on',
            'Deposited by',
            'Deposited at',
            'Release approved by',
            'Release approved at',
            'Refund approved by',
            'Refund approved at',
            'Refunded by',
            'Refunded at',
            'Issuer',
            'COC',
            'Flawed',
            'Day limit',
            'VIN',
            'Vehicle condition',
            'Office',
            'Merchant',
            'Vehicle type',
            'Vehicle color',
            'Vehicle make',
            'Vehicle model',
            'Status at',
        ], 'PledgesReports' . now() . '.xlsx');
    }

    private function getQuery()
    {
        $queryBuilder = JQueryBuilder::for(Pledge::class)
            ->join('users as releaseApproverUser', 'releaseApproverUser.id', '=', 'pledges.release_approved_by', 'LEFT OUTER')
            ->join('users as refundApproverUser', 'refundApproverUser.id', '=', 'pledges.refund_approved_by', 'LEFT OUTER')
            ->join('users as refundUser', 'refundUser.id', '=', 'pledges.refunded_by', 'LEFT OUTER')
            ->join('users as depositUser', 'depositUser.id', '=', 'pledges.deposited_by', 'LEFT OUTER')
            ->join('vehicles', 'vehicles.vin', '=', 'pledges.vin', 'LEFT OUTER')
            ->join('vehicle_models', 'vehicle_models.id', '=', 'vehicles.vehicle_model_id', 'LEFT OUTER')
            ->join('vehicle_makes', 'vehicle_makes.id', '=', 'vehicle_models.vehicle_make_id', 'LEFT OUTER')
            ->join('pledge_payments', 'pledge_payments.pledge_id', '=', 'pledges.id', 'LEFT OUTER')
            ->join('users as receiverUser', 'receiverUser.id', '=', 'pledge_payments.received_by', 'LEFT OUTER')
            ->allowedFilters([
                'pledges.id',
                'pledges.office',
                'pledges.merchant',
                'pledges.vin',
                AllowedFilter::custom('pledges.pledge_date', new BetweenFilter),
                'pledges.issuer',
                'pledges.coc',
                'pledges.flawed',
                'pledges.remarks',
                'pledges.amount',
                'pledges.day_limit',
                'pledges.amount_status',
                'pledges.amount_status_change_date',
                'pledges.deposited_by_user',
                'pledges.deposited_at',
                'pledges.release_approved_by_user',
                'pledges.release_approved_at',
                'pledges.refund_approved_by_user',
                'pledges.refund_approved_at',
                'pledges.refunded_by_user',
                'pledges.refunded_at',
                'vehicles.condition',
                'vehicles.type',
                'vehicle_makes.name',
                'vehicle_models.name',
                'vehicles.color',
                'pledge_payments.amount',
                'pledge_payments.date_time',
                'pledge_payments.revoked_on',
                'pledge_payments.received_by',
                'pledge_payments.received_at',

            ])
            ->selectRaw('
                    pledges.id,
                    pledges.office,
                    pledges.merchant,
                    pledges.vin,
                    vehicles.condition as vehicle_condition,
                    vehicles.type as vehicle_type,
                    vehicles.color as vehicle_color,
                    vehicle_models.name as vehicle_model,
                    vehicle_makes.name as vehicle_make,
                    pledges.pledge_date,
                    pledges.issuer,
                    pledges.coc,
                    pledges.flawed,
                    pledges.remarks,
                    pledges.amount,
                    pledges.day_limit,
                    pledges.amount_status,
                    pledges.amount_status_change_date,
                    depositUser.name as deposited_by_user,
                    pledges.deposited_at,
                    releaseApproverUser.name as release_approved_by_user,
                    pledges.release_approved_at,
                    refundApproverUser.name as refund_approved_by_user,
                    pledges.refund_approved_at,
                    refundUser.name as refunded_by_user,
                    pledges.refunded_at,

                    pledge_payments.id as pledge_payment_amount_id,
                    pledge_payments.amount as pledge_payment_amount,
                    pledge_payments.revoked_on as  pledge_payment_revoked_on,
                    pledge_payments.received_at as  pledge_payment_received_at,
                    receiverUser.name as pledge_payment_received_by_user
                ')
            ->orderBy('pledges.id', 'asc');

        return $queryBuilder;
    }
}
