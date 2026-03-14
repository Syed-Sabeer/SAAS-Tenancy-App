<?php

namespace Database\Seeders;

use App\Models\Central\EnterpriseAdmin;
use App\Models\Central\Plan;
use App\Support\EnterpriseAdminStatuses;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CentralDatabaseSeeder extends Seeder
{
    public function run()
    {
        EnterpriseAdmin::updateOrCreate(
            ['email' => env('ENTERPRISE_ADMIN_EMAIL', 'admin@saas.local')],
            [
                'name' => env('ENTERPRISE_ADMIN_NAME', 'Enterprise Admin'),
                'password' => Hash::make(env('ENTERPRISE_ADMIN_PASSWORD', 'Password@123')),
                'status' => EnterpriseAdminStatuses::ACTIVE,
            ]
        );

        Plan::updateOrCreate(
            ['code' => 'starter-monthly'],
            [
                'name' => 'Starter',
                'price' => 29.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'users_limit' => 10,
                    'projects_limit' => 25,
                    'support' => 'email',
                ],
                'status' => true,
            ]
        );

        Plan::updateOrCreate(
            ['code' => 'growth-monthly'],
            [
                'name' => 'Growth',
                'price' => 79.00,
                'billing_cycle' => 'monthly',
                'features' => [
                    'users_limit' => 50,
                    'projects_limit' => 200,
                    'support' => 'priority-email',
                ],
                'status' => true,
            ]
        );

        Plan::updateOrCreate(
            ['code' => 'enterprise-yearly'],
            [
                'name' => 'Enterprise',
                'price' => 1499.00,
                'billing_cycle' => 'yearly',
                'features' => [
                    'users_limit' => 'unlimited',
                    'projects_limit' => 'unlimited',
                    'support' => 'dedicated-manager',
                ],
                'status' => true,
            ]
        );
    }
}