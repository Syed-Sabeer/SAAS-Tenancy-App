<?php

namespace App\Services;

use App\Models\Tenant\Role;
use App\Models\Tenant\User;
use App\Support\TenantUserRoles;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class TenantUserService
{
	/**
	 * @param array<string, mixed> $data
	 */
	public function createUser(array $data): User
	{
		$role = Arr::get($data, 'role', TenantUserRoles::COMPANY_USER);
		$this->assertRoleAllowed($role);

		return DB::connection('tenant')->transaction(function () use ($data, $role) {
			$user = User::create([
				'name' => Arr::get($data, 'name'),
				'email' => Arr::get($data, 'email'),
				'password' => Hash::make(Arr::get($data, 'password')),
				'role' => $role,
				'status' => Arr::get($data, 'status', 'active'),
				'phone' => Arr::get($data, 'phone'),
				'avatar' => Arr::get($data, 'avatar'),
			]);

			$this->syncRole($user, $role);

			return $user;
		});
	}

	/**
	 * @param array<string, mixed> $data
	 */
	public function updateUser(User $user, array $data): User
	{
		$role = Arr::get($data, 'role', $user->role);
		$this->assertRoleAllowed($role);

		return DB::connection('tenant')->transaction(function () use ($user, $data, $role) {
			$payload = [
				'name' => Arr::get($data, 'name', $user->name),
				'email' => Arr::get($data, 'email', $user->email),
				'role' => $role,
				'status' => Arr::get($data, 'status', $user->status),
				'phone' => Arr::get($data, 'phone', $user->phone),
				'avatar' => Arr::get($data, 'avatar', $user->avatar),
			];

			if (Arr::has($data, 'password') && Arr::get($data, 'password')) {
				$payload['password'] = Hash::make(Arr::get($data, 'password'));
			}

			$user->update($payload);
			$this->syncRole($user, $role);

			return $user->fresh();
		});
	}

	public function deactivateUser(User $user): bool
	{
		return DB::connection('tenant')->transaction(function () use ($user) {
			return $user->update(['status' => 'inactive']);
		});
	}

	public function deleteUser(User $user): bool
	{
		return DB::connection('tenant')->transaction(function () use ($user) {
			$user->roles()->detach();
			return (bool) $user->delete();
		});
	}

	protected function assertRoleAllowed(string $role): void
	{
		if (!in_array($role, TenantUserRoles::all(), true)) {
			throw new InvalidArgumentException('Invalid tenant user role: ' . $role);
		}
	}

	protected function syncRole(User $user, string $roleName): void
	{
		$role = Role::firstOrCreate([
			'name' => $roleName,
			'guard_name' => 'tenant',
		]);

		$user->roles()->sync([$role->id]);
	}
}
