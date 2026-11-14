<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'users:make-admin {email : Email address of the existing user}';

    protected $description = 'Grant administrator access to an existing user';

    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->components->error('No active user found with that email address.');

            return self::FAILURE;
        }

        $user->forceFill(['is_admin' => true])->save();
        $this->components->info("Administrator access granted to {$user->email}.");

        return self::SUCCESS;
    }
}
