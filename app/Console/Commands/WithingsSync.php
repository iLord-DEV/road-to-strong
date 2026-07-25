<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Modules\Withings\MeasurementImporter;
use Illuminate\Console\Command;

class WithingsSync extends Command
{
    protected $signature = 'withings:sync';

    protected $description = 'Import new Withings body measurements for all connected users';

    public function handle(MeasurementImporter $importer): int
    {
        foreach (User::whereHas('oauthTokens', fn ($q) => $q->where('provider', 'withings'))->get() as $user) {
            $count = $importer->import($user);
            $this->info("{$user->email}: {$count} Messungen synchronisiert.");
        }

        return self::SUCCESS;
    }
}
