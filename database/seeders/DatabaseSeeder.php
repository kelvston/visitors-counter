<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(?string $adminPassword = null): void
    {
        if ($adminPassword === null) {
            if (! isset($this->command)) {
                throw new RuntimeException('Provide an admin password when running this seeder programmatically.');
            }

            $adminPassword = $this->command->secret('Enter password for admin@aru.ac.tz');
        }

        if (! is_string($adminPassword) || $adminPassword === '') {
            throw new RuntimeException('The admin password cannot be empty.');
        }

        $admin = User::withTrashed()->firstOrNew(['email' => 'admin@aru.ac.tz']);
        $admin->forceFill([
            'name' => 'ARU Administrator',
            'password' => $adminPassword,
            'is_admin' => true,
            'staff_category' => 'academic',
            'email_verified_at' => now(),
        ])->save();

        if ($admin->trashed()) {
            $admin->restore();
        }

    }
}
