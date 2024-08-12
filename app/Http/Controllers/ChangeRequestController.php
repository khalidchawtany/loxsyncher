<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Http\Requests\ChangeRequests\StoreChangeRequest;
use App\Http\Requests\ChangeRequests\UpdateChangeRequest;
use App\Mail\ChangeRequested;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Adapters\JQueryBuilder;

class ChangeRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']);
        $this->middleware('permission:view_change_request', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_change_request', ['only' => ['create']]);
        $this->middleware('permission:update_change_request', ['only' => ['update']]);
        $this->middleware('permission:destroy_change_request', ['only' => ['destroy']]);
    }

    public function index()
    {
        return view('change_requests.index');
    }

    public function list(Request $request)
    {
        return JQueryBuilder::for(ChangeRequest::class)
            ->join('users', 'users.id', '=', 'change_requests.user_id', 'LEFT OUTER')
            ->allowedFilters([
                'reason',
                'requested_for',
                'users.kurdish_name',
                'permission_name',
                'description',
                'status',
                'note',
                'user_id',
            ])
            ->selectRaw('
        change_requests.*,
        users.kurdish_name as requested_by
      ')
            ->when(!$request->filled(['sort', 'order']), function ($query) {
                return $query->orderBy('id', 'desc');
            })
            ->jsonJPaginate();
    }

    public function showChangeRequestDialog(Request $request)
    {
        if ($request->has('id')) {
            $change_request = ChangeRequest::findOrFail($request->id);

            return view('change_requests.change_request_dialog', ['change_request' => $change_request]);
        }

        return view('change_requests.change_request_dialog');
    }

    public function showChangeRequestViewDialog(Request $request)
    {
        $change_request = ChangeRequest::with(['user'])
            ->findOrFail($request->id);

        return view('change_requests.change_request_view_dialog', [
            'change_request' => $change_request,
        ]);
    }

    protected function create(StoreChangeRequest $request)
    {
        $change_request = ChangeRequest::create($request->input());

        Mail::to(['falah.hassan@loxqc.com'])
            ->send(new ChangeRequested($change_request));

        return ezReturnSuccessMessage('Change Request created successfully!', $change_request->id);
    }

    public function update(UpdateChangeRequest $request)
    {
        $change_request = ChangeRequest::findOrFail($request->id);

        if ($change_request->status !== null) {
            return ezReturnErrorMessage('You cannot change a PR after status change!');
        }

        $change_request->update($request->input());

        return ezReturnSuccessMessage('Change Request updated successfully!');
    }

    public function destroy(Request $request)
    {
        $change_request = ChangeRequest::findOrFail($request->id);

        $change_request->delete();

        return ezReturnSuccessMessage('Change Request removed successfully!');
    }

    public function toggleChangeRequestStatus(Request $request)
    {
        $change_request = ChangeRequest::findOrFail($request->id);

        if (
            ($request->status == 0 && !auth()->user()->can('reject_change_request'))
            || ($request->status == 1 && !auth()->user()->can('approve_change_request'))
            || ($request->status == 2 && !auth()->user()->can('grant_change_request'))
            || $request->status > 2
        ) {
            return ezReturnErrorMessage("You don't have permission to perform this task!");
        }

        $change_request->status = $request->status;
        $change_request->reason = $request->reason;
        $change_request->save();

        return ezReturnSuccessMessage('Change Request updated successfully!');
    }
}
