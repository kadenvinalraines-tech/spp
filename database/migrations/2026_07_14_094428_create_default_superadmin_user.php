<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        
        $permissions = [
            'manage_users', 'manage_roles', 'manage_settings',
            'manage_academic_years', 'manage_classes', 'manage_students',
            'manage_promotions', 'manage_finance_posts', 'manage_bills',
            'manage_payments', 'manage_expenses', 'view_reports',
            'view_audit_trails', 'view_dashboard_stats'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        
        $role->permissions()->sync(Permission::pluck('id')->toArray());

        $user = User::firstOrCreate(
            ['email' => 'superadmin@sekolah.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password')
            ]
        );

        if (!$user->hasRole('Super Admin')) {
            $user->roles()->attach($role->id);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_superadmin_user');
    }
};
