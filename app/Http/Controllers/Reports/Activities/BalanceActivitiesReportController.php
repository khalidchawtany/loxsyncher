<?php

namespace App\Http\Controllers\Reports\Activities;

use App\Models\Balance;
use App\Http\Controllers\Reports\BaseReportController;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use App\Adapters\JQueryBuilder;

class BalanceActivitiesReportController extends BaseReportController
{
  public function __construct()
  {
    parent::__construct();
  }


  public function showBalancesActivityReport()
  {
    return view('reports.activities.balances');
  }

  public function listBalancesActivityReport(Request $request)
  {
    if (getFilterRule('balances.created_at') == null) {
      return ezReturnErrorMessage('Select a date range');
    }

    $paginatedRows = JQueryBuilder::for(Balance::class)
      ->join('users', 'users.id', 'balances.user_id')
      ->join('products', 'products.id', '=', 'balances.product_id', 'LEFT OUTER')
      ->selectRaw('
                    balances.id as balance_id,
                    users.kurdish_name as user_name,
                    balances.product_type,
                    products.kurdish_name as product_name,
                    balances.created_at
                ')
      ->allowedFilters(
        "balances.id",
        "balances.created_at",
        "balances.update_count",
        "balances.product_type",
        "products.kurdish_name",
        "users.kurdish_name"
      )
      ->when(!$request->filled(['sort', 'order']), function ($query) {
        return $query->orderBy('balances.id', 'desc');
      })
      ->jsonJPaginate();

    $rows = collect($paginatedRows['rows']);

    $balance_ids = $rows->pluck('balance_id');

    $history = Activity::whereIn('subject_id', $balance_ids)
      ->where(['subject_type' => 'App\\Models\\Balance'])
      ->join('users', 'users.id', 'activity_log.causer_id')
      ->selectRaw('
                  activity_log.subject_id,
                  activity_log.created_at as date,
                  users.kurdish_name as user_name,
                  activity_log.properties as props
                ')
      ->get();

    $historyGroupedById = $history->groupBy('subject_id')->toArray();

    $rows = $rows->map(function ($row) use ($historyGroupedById) {
      $updates = [];
      if (isset($historyGroupedById[$row['balance_id']])) {
        $row['update_count'] = count($historyGroupedById[$row['balance_id']]);
        $updates = $historyGroupedById[$row['balance_id']];
      }
      /* $updates = collect($updates)->map(function ($update) { */
      /*   $props = json_decode($update['props']); */
      /*   $old = trim(prettyPrint(json_encode($props->old)), '{}\t\n\r\0\x0B"'); */
      /*   $new = trim(prettyPrint(json_encode($props->attributes)), '{}\t\n\r\0\x0B"'); */
      /*   $update['old'] = $old; */
      /*   $update['new'] = $new; */
      /*   return $update; */
      /* }); */

      $row['updates'] = $updates;
      return $row;
    });

    $paginatedRows['rows'] = $rows;

    return array_merge($paginatedRows, ['stat_rows' => []]);
  }
}
