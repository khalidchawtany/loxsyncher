<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequests\StorePermissionRequest;
use App\Http\Requests\PermissionRequests\UpdatePermissionRequest;
use App\Mail\PermissionRequested;
use App\Models\PermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Adapters\JQueryBuilder;

class PermissionRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_permission_request', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_permission_request', ['only' => ['create']]);
        $this->middleware('permission:update_permission_request', ['only' => ['update']]);
        $this->middleware('permission:destroy_permission_request', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('permission_requests.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(PermissionRequest::class)
            ->join('users', 'users.id', '=', 'permission_requests.requested_for', 'LEFT OUTER')
            ->join('users as requester_users', 'requester_users.id', '=', 'permission_requests.user_id', 'LEFT OUTER')
            ->allowedFilters([
                'reason',
                'requested_for',
                'users.kurdish_name',
                'requester_users.kurdish_name',
                'permission_name',
                'description',
                'status',
                'note',
                'user_id',
            ])
            ->selectRaw('
        permission_requests.*,
        users.kurdish_name as requested_for_name,
        requester_users.kurdish_name as requested_by
      ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('id', 'desc');
            })
            ->jsonJPaginate();
    }

    public function showPermissionRequestDialog(Request $request)
    {
        if ($request->has('id')) {
            $permission_request = PermissionRequest::findOrFail($request->id);

            return view('permission_requests.permission_request_dialog', ['permission_request' => $permission_request]);
        }

        return view('permission_requests.permission_request_dialog');
    }

    public function showPermissionRequestViewDialog(Request $request)
    {
        $permission_request = PermissionRequest::with(['for_user', 'user'])
            ->findOrFail($request->id);

        return view('permission_requests.permission_request_view_dialog', [
            'permission_request' => $permission_request,
        ]);
    }

    public function showPermissionRequestRejectDialog(Request $request)
    {
        $permission_request = PermissionRequest::with(['for_user', 'user'])
            ->findOrFail($request->id);

        return view('permission_requests.permission_request_reject_dialog', [
            'permission_request' => $permission_request,
        ]);
    }

    protected function create(StorePermissionRequest $request)
    {
        $permission_request = PermissionRequest::create($request->input());

        Mail::to(['falah.hassan@loxqc.com'])
            ->send(new PermissionRequested($permission_request));

        return ezReturnSuccessMessage('Permission Request created successfully!', $permission_request->id);
    }

    public function update(UpdatePermissionRequest $request)
    {
        $permission_request = PermissionRequest::findOrFail($request->id);

        if ($permission_request->status !== null) {
            return ezReturnErrorMessage('You cannot change a PR after status change!');
        }

        $permission_request->update($request->input());

        return ezReturnSuccessMessage('Permission Request updated successfully!');
    }

    public function destroy(Request $request)
    {
        $permission_request = PermissionRequest::findOrFail($request->id);

        $permission_request->delete();

        return ezReturnSuccessMessage('Permission Request removed successfully!');
    }

    public function togglePermissionStatus(Request $request)
    {
        $permission_request = PermissionRequest::findOrFail($request->id);

        if (
            ($request->status == 0 && !auth()->user()->can('reject_permission_request'))
            || ($request->status == 1 && !auth()->user()->can('approve_permission_request'))
            || ($request->status == 2 && !auth()->user()->can('grant_permission_request'))
            || $request->status > 2
        ) {
            return ezReturnErrorMessage("You don't have permission to perform this task!");
        }

        $permission_request->status = $request->status;
        $permission_request->reason = $request->reason;
        $permission_request->save();

        return ezReturnSuccessMessage('Permission Request updated successfully!');
    }
}
