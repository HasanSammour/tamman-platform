<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ==================== CREATE PERMISSIONS ====================
        
        // User Permissions (Patient)
        $userPermissions = [
            'view_dashboard',
            'view_profile',
            'edit_profile',
            'search_specialist',
            'view_specialist_profile',
            'book_session',
            'cancel_session',
            'view_my_sessions',
            'join_session',
            'log_mood',
            'view_mood_history',
            'take_test',
            'view_test_results',
            'view_points',
            'earn_points',
            'redeem_points',
            'send_message',
            'view_messages',
            'view_treatment_plan',
            'complete_treatment_task',
            'view_content',
            'rate_specialist',
            'use_referral_code',
            'view_emergency_resources',
            'request_financial_aid',
            'view_donor_credits',
        ];

        // Specialist Permissions
        $specialistPermissions = [
            'view_specialist_dashboard',
            'edit_specialist_profile',
            'manage_availability',
            'view_upcoming_sessions',
            'host_session',
            'view_session_history',
            'view_patient_mood_history',
            'view_patient_test_results',
            'create_treatment_plan',
            'edit_treatment_plan',
            'assign_tasks',
            'view_treatment_plan_progress',
            'send_message_to_patient',
            'view_earnings',
            'upload_credentials',
            'add_session_notes',
            'view_patient_list',
        ];

        // Admin Permissions
        $adminPermissions = [
            'view_admin_dashboard',
            'manage_all_users',
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'suspend_user',
            'activate_user',
            'verify_specialist',
            'approve_specialist',
            'reject_specialist',
            'manage_specialist_applications',
            'manage_donor_credits',
            'allocate_credits',
            'view_credit_transactions',
            'manage_content',
            'create_content',
            'edit_content',
            'delete_content',
            'publish_content',
            'view_analytics',
            'view_system_logs',
            'manage_platform_settings',
            'view_all_sessions',
            'manage_specialist_payments',
            'process_payments',
            'manage_emergency_resources',
            'view_financial_reports',
            'export_reports',
            'send_notifications_to_all',
        ];

        // Donor Permissions
        $donorPermissions = [
            'view_donor_dashboard',
            'make_donation',
            'view_donation_history',
            'view_impact_reports',
            'view_credit_usage',
        ];

        // Merge all permissions
        $allPermissions = array_merge(
            $userPermissions,
            $specialistPermissions,
            $adminPermissions,
            $donorPermissions
        );

        // Create permissions
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ==================== CREATE ROLES ====================

        // Patient Role
        $patientRole = Role::firstOrCreate(['name' => 'patient', 'guard_name' => 'web']);
        $patientRole->syncPermissions($userPermissions);

        // Specialist Role
        $specialistRole = Role::firstOrCreate(['name' => 'specialist', 'guard_name' => 'web']);
        $specialistRole->syncPermissions($specialistPermissions);

        // Admin Role (gets all permissions)
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($allPermissions);

        // Donor Role
        $donorRole = Role::firstOrCreate(['name' => 'donor', 'guard_name' => 'web']);
        $donorRole->syncPermissions($donorPermissions);

        // ==================== OUTPUT SUMMARY ====================
        
        $this->command->info('========================================');
        $this->command->info('Roles and Permissions Seeding Completed!');
        $this->command->info('========================================');
        $this->command->info('Roles Created:');
        $this->command->info('  - patient (' . count($userPermissions) . ' permissions)');
        $this->command->info('  - specialist (' . count($specialistPermissions) . ' permissions)');
        $this->command->info('  - admin (' . count($allPermissions) . ' permissions)');
        $this->command->info('  - donor (' . count($donorPermissions) . ' permissions)');
        $this->command->info('========================================');
    }
}