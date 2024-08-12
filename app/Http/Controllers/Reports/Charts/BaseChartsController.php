<?php

namespace App\Http\Controllers\Reports\Charts;

use App\Http\Controllers\Reports\BaseReportController;

class BaseChartsController extends BaseReportController
{
    public function __construct()
    {
        parent::__construct();
        // $this->middleware('permission:view_charts', ['only' => ['index']]);
    }
}
