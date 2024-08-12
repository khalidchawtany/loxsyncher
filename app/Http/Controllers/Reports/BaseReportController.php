<?php

namespace App\Http\Controllers\Reports;
use App\Utils\FilterRulesHelper;

use App\Models\Category;
use App\Models\Department;
use App\Http\Controllers\Controller;
use App\Traits\DownloadsExcelFileTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaseReportController extends Controller
{
    use DownloadsExcelFileTrait;

    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_report', ['only' => ['index']]);
    }

    public function index()
    {
        return view('reports.layout');
    }

    public function listDepartments(Request $request)
    {
        $departments = Department::selectRaw('distinct(name) as department_name, id as department_id')
            ->whereIn('departments.id', request()->user_departments)
            ->get()
            ->toArray();

        return array_merge([['department_name' => 'All', 'department_id' => 0]], $departments);
    }

    public function listProductCategories(Request $request)
    {
        return Category::selectRaw('id, name')
            ->when($request->filled('q'), function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->q}%");
            })
            ->take(10)->get()->prepend((object) ['id' => 0, 'name' => 'All']);
    }

    protected function getDateRangeFromFilterRules($filter = 'payment_date')
    {
        $from = null;
        $to = null;

        $value = FilterRulesHelper::get($filter);

        if (str_contains($value, ',')) {
            $from = str_before($value, ',');
            $to = str_after($value, ',');
        }

        return (object) [
            'from' => Carbon::parse($from),
            'to' => Carbon::parse($to)->subDay(),
        ];
    }

    protected function getDatesInDateRange($dateRange)
    {
        $dates = [];

        for ($date = clone $dateRange->from; $date->lte($dateRange->to); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    protected function valiateDaterange($permissionName, $dateRange)
    {
        $user = auth()->user();

        $hasView = $user->can($permissionName);
        if (!$hasView) {
            return false;
        }

        // permission_anydate can see everything
        $hasAnyDateRangeView = $user->can($permissionName . '_for_any_date_range');
        if ($hasAnyDateRangeView) {
            return true;
        }

        // permission_extended can see 2 months  and only prev 2 months
        $hasExtendedDateRangeView = $user->can($permissionName . '_for_extended_date_range');
        if (
            $hasExtendedDateRangeView
            && !$this->dateRangeWiderThanNumberOfDays($dateRange, 62)
            && !$this->dateRangeOlderThanPermittedNumberOfDays($dateRange, 62)
        ) {
            return true;
        }

        // normal user can see 2 days and only previous 2 days.
        // This is number 3 since two days before todays equals 3
        return !$this->dateRangeWiderThanNumberOfDays($dateRange, 2)
            && !$this->dateRangeOlderThanPermittedNumberOfDays($dateRange, 3);
    }

    protected function dateRangeWiderThanNumberOfDays($dateRange, $days)
    {
        return $dateRange->from->diffInDays($dateRange->to) >= $days;
    }

    protected function dateRangeOlderThanPermittedNumberOfDays($dateRange, $days)
    {
        return $dateRange->from->diffInDays(now()) >= $days
            || $dateRange->to->diffInDays(now()) >= $days;
    }
}
