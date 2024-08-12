<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Http\Requests\AppSettings\StoreAppSetting;
use App\Http\Requests\AppSettings\UpdateAppSetting;
use Illuminate\Http\Request;
use App\Adapters\JQueryBuilder;

class AppSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_app_setting')->only(['index', 'list']);
        $this->middleware('permission:create_app_setting')->only(['create']);
        $this->middleware('permission:update_app_setting')->only(['update']);
        $this->middleware('permission:destroy_app_setting')->only(['destroy']);
    }

    public function index()
    {
        return view('app_settings.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(AppSetting::class)
            ->allowedFilters('name', 'value', 'user_id')
            ->jsonJPaginate();
    }

    protected function create(StoreAppSetting $request)
    {
        $app_setting = AppSetting::create($request->input());

        return ezReturnSuccessMessage('App Setting created successfully!', $app_setting);
    }

    public function update(UpdateAppSetting $request)
    {
        $app_setting = AppSetting::findOrFail($request->id);

        $app_setting->update($request->input());

        return ezReturnSuccessMessage('App Setting updated successfully!');
    }

    public function destroy(Request $request)
    {
        $app_setting = AppSetting::findOrFail($request->id);

        $app_setting->delete();

        return ezReturnSuccessMessage('App Setting removed successfully!');
    }
}
