<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Concerns\RespondsWithJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UserStoreRequest;
use App\Http\Requests\Tenant\UserUpdateRequest;
use App\Models\Tenant\User;
use App\Services\TenantUserService;
use Throwable;

class UserController extends Controller
{
    use RespondsWithJson;

    /**
     * @var \App\Services\TenantUserService
     */
    protected $tenantUserService;

    public function __construct(TenantUserService $tenantUserService)
    {
        $this->tenantUserService = $tenantUserService;
        $this->middleware('tenant.role:company_admin');
    }

    public function index()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        $users = User::query()->latest('id')->paginate(20);

        return $this->success('Tenant users fetched successfully.', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        return $this->success('Tenant user create payload.', [
            'allowed_roles' => ['company_admin', 'company_user'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\UserStoreRequest  $request
     */
    public function store(UserStoreRequest $request)
    {
        try {
            $user = $this->tenantUserService->createUser($request->validated());

            return $this->success('Tenant user created successfully.', [
                'user' => $user,
            ], 201);
        } catch (Throwable $exception) {
            return $this->fail('Unable to create tenant user.', 422, [
                'exception' => [$exception->getMessage()],
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tenant\User  $user
     */
    public function edit(User $user)
    {
        if (!request()->expectsJson()) {
            return view('welcome');
        }

        return $this->success('Tenant user edit payload fetched successfully.', [
            'user' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Tenant\UserUpdateRequest  $request
     * @param  \App\Models\Tenant\User  $user
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        try {
            $updated = $this->tenantUserService->updateUser($user, $request->validated());

            return $this->success('Tenant user updated successfully.', [
                'user' => $updated,
            ]);
        } catch (Throwable $exception) {
            return $this->fail('Unable to update tenant user.', 422, [
                'exception' => [$exception->getMessage()],
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tenant\User  $user
     */
    public function destroy(User $user)
    {
        $currentUser = auth('tenant')->user();

        if ((int) $currentUser->id === (int) $user->id) {
            return $this->fail('You cannot delete your own account.', 422, [
                'user' => ['Self-delete is not allowed.'],
            ]);
        }

        $this->tenantUserService->deleteUser($user);

        return $this->success('Tenant user deleted successfully.');
    }
}
