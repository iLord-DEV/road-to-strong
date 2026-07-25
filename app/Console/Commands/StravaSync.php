<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Strava\ActivityImporter;
use Illuminate\Console\Command;

class StravaSync extends Command
{
    protected $signature = 'strava:sync';

    protected $description = 'Import new Strava activities for all connected users';

    public function handle(ActivityImporter $importer): int
    {
        foreach (User::whereHas('oauthTokens', fn ($q) => $q->where('provider', 'strava'))->get() as $user) {
            $count = $importer->import($user);
            $this->info("{$user->email}: {$count} Aktivitäten synchronisiert.");
        }

        return self::SUCCESS;
    }
}
