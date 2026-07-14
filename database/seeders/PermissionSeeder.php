<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'manage_users',
            'manage_roles',
            'manage_settings',
            'manage_academic_years',
            'manage_classes',
            'manage_students',
            'manage_promotions',
            'manage_finance_posts',
            'manage_bills',
            'manage_payments',
            'manage_expenses',
            'view_reports',
            'view_audit_trails',
            'view_dashboard_stats'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}
