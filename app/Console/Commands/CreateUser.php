<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateUser extends Command
{
    protected $signature = 'app:create-user {email} {--name=}';

    protected $description = 'Create (or reset the password of) the app user';

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->secret('Passwort');

        if (strlen((string) $password) < 8) {
            $this->error('Das Passwort muss mindestens 8 Zeichen lang sein.');

            return self::FAILURE;
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $this->option('name') ?? strstr($email, '@', true),
                'password' => $password,
            ],
        );

        $this->info($user->wasRecentlyCreated ? "Benutzer {$email} angelegt." : "Passwort für {$email} aktualisiert.");

        return self::SUCCESS;
    }
}
