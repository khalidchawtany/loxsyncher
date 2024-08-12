<?php

namespace App\Http\Controllers\UserManager;

use App\Adapters\JQueryBuilder;
use App\Exceptions\AjaxException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\StoreUser;
use App\Http\Requests\Users\UpdateUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\QueryBuilder\AllowedFilter;
use App\Filters\ExcludeFilter;

class UserController extends Controller
{
    /*
      |--------------------------------------------------------------------------
      | User Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the creation of new users as well as their
      | validation and creation throw admin panel.
      |
       */

    // use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'user.id']); // allow creating through admin only
        $this->middleware('permission:view_user', ['only' => ['index', 'list']]);
        $this->middleware('permission:create_user', ['only' => ['create']]);
        $this->middleware('permission:update_user', ['only' => ['update']]);
        $this->middleware('permission:destroy_user', ['only' => ['destroy']]);
        $this->middleware('permission:reset_user_password', ['only' => ['resetPassword']]);
        $this->middleware('permission:toggle_user_status', ['only' => ['toggleStatus']]);
        $this->middleware('permission:impersonate_user', ['only' => ['impersonateUser']]);
    }

    public function index()
    {
        return view('auth.index');
    }

    public function list(Request $request)
    {
        $query = User::with(['roles:id,name', 'permissions:id,name']);

        if (!Auth::user()->hasrole('Super')) {
            $query = $query->where('name', '<>', 'Super');
        }

        $roleFilter = getFilterRule('roles');
        if (!empty($roleFilter)) {
            $roles = Role::where('name', 'like', $roleFilter . '%');
            $query->role($roles->get());
        }

        $query->orderBy('name', 'asc');

        return JQueryBuilder::for($query)
            ->allowedFilters(
                'id',
                'name',
                'kurdish_name',
                'email',
                'status',
                'job_description',
                AllowedFilter::exact('is_staff'),
                AllowedFilter::exact('external_view'),
                AllowedFilter::exact('external_update'),
                AllowedFilter::custom('roles', new ExcludeFilter),
            )->jsonJPaginate();
    }

    public function jsonList(Request $request)
    {
        return User::select(['id', 'kurdish_name', 'email'])
            ->when($request->filled('q'), function ($q) use ($request) {
                return $q->where('name', 'like', "{$request->q}%");
            })->take(10)->get();
    }

    public function listRoles(Request $request)
    {
        return Role::where('name', '<>', 'Super')
            ->get();
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    public function create(StoreUser $request)
    {
        $user = User::create([
            'name' => $request->name,
            'kurdish_name' => $request->kurdish_name,
            'email' => $request->email,
            'password' => Hash::make($request->email),
            'open_transaction_after_login' => $request->open_transaction_after_login,
            'is_staff' => $request->is_staff,
            'job_description' => $request->job_description,
            'external_view' => $request->external_view,
            'external_update' => $request->external_update,
        ]);

        $user->assignRole($request->role);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties($user->toArray())
            ->log('created');

        return ezReturnSuccessMessage('User created successfully!');
    }

    public function update(UpdateUser $request)
    {
        $user = User::findOrFail($request->id);
        $user->update($request->only([
            'name',
            'kurdish_name',
            'email',
            'open_transaction_after_login',
            'is_staff',
            'job_description',
            'external_view',
            'external_update',
        ]));

        $user->syncRoles($request->role);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties($user->toArray())
            ->log('updated');

        return ezReturnSuccessMessage('User updated successfully!');
    }

    public function destroy(Request $request)
    {
        $user = User::findOrFail($request->id);

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties($user->toArray())
            ->log('deleted');

        $user->delete();

        return ezReturnSuccessMessage('User removed successfully!');
    }

    public function toggleStatus(Request $request)
    {
        $user = User::findOrFail($request->id);

        $user->status = 1 - intval($user->status);

        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties($user->toArray())
            ->log('updated');

        return ezReturnSuccessMessage('User staus changed successfully!');
    }

    public function resetPassword(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->password = Hash::make('123456');
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties($user->toArray())
            ->log('updated');

        return ezReturnSuccessMessage('User password reset successfully!');
    }

    public function impersonateUser(Request $request)
    {
        throw_if(empty($request->id), new AjaxException('No user specified'));

        $user = User::findOrFail($request->id);

        Auth::user()->impersonate($user);

        return ezReturnSuccessMessage("You impersonating {$user->name}");
    }

    public function leaveImpersonation()
    {
        Auth::user()->leaveImpersonation();

        return redirect('/home');
    }
}
