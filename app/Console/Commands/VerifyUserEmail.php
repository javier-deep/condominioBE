<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class VerifyUserEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify-email {email?} {--all : Verify all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify user email addresses or create test users with verified emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->verifyAllUsers();
            return;
        }

        $email = $this->argument('email');
        
        if (!$email) {
            $this->createTestUsers();
            return;
        }

        $this->verifyUserByEmail($email);
    }

    private function verifyAllUsers()
    {
        $users = User::whereNull('email_verified_at')->get();
        
        if ($users->isEmpty()) {
            $this->info('All users already have verified emails.');
            return;
        }

        foreach ($users as $user) {
            $user->email_verified_at = now();
            $user->save();
            $this->info("Email verified for: {$user->email}");
        }

        $this->info("Verified {$users->count()} user emails.");
    }

    private function verifyUserByEmail($email)
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found.");
            return;
        }

        if ($user->hasVerifiedEmail()) {
            $this->info("User {$email} already has a verified email.");
            return;
        }

        $user->email_verified_at = now();
        $user->save();
        
        $this->info("Email verified successfully for: {$email}");
    }

    private function createTestUsers()
    {
        $this->info('Creating test users with verified emails...');

        // Admin user
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@condominio.com'],
            [
                'name' => 'Admin Usuario',
                'email' => 'admin@condominio.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'phone' => '1234567890',
                'apartment_number' => 'A101',
                'address' => 'Condominio Central'
            ]
        );

        // Residente user
        $residentUser = User::updateOrCreate(
            ['email' => 'residente@condominio.com'],
            [
                'name' => 'Usuario Residente',
                'email' => 'residente@condominio.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'is_active' => true,
                'phone' => '0987654321',
                'apartment_number' => 'B205',
                'address' => 'Condominio Central'
            ]
        );

        // Assign roles if they exist
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole && !$adminUser->roles()->where('role_id', $adminRole->id)->exists()) {
            $adminUser->roles()->attach($adminRole);
        }

        $residentRole = Role::where('name', 'resident')->first();
        if ($residentRole && !$residentUser->roles()->where('role_id', $residentRole->id)->exists()) {
            $residentUser->roles()->attach($residentRole);
        }

        $this->table(
            ['Email', 'Password', 'Role', 'Verified'],
            [
                ['admin@condominio.com', 'password123', 'Admin', 'Yes'],
                ['residente@condominio.com', 'password123', 'Resident', 'Yes']
            ]
        );

        $this->info('Test users created successfully!');
        $this->info('You can now login with either account using password: password123');
    }
}
