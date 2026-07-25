<?php

namespace App\Http\Controllers;

use App\Modules\Strava\ActivityImporter;
use App\Modules\Strava\StravaClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StravaAuthController extends Controller
{
    public function __construct(
        private readonly StravaClient $client,
    ) {}

    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('oauth_state.strava', $state);

        return redirect()->away(
            $this->client->authorizeUrl(route('strava.callback'), $state),
        );
    }

    public function callback(Request $request, ActivityImporter $importer): RedirectResponse
    {
        $state = $request->session()->pull('oauth_state.strava');

        if ($request->query('error') !== null) {
            return redirect()->route('dashboard')->with('error', 'Strava-Verbindung abgebrochen.');
        }

        // A duplicate callback (reload, double redirect) finds no state in the
        // session because it was pulled on the first call — don't hard-fail.
        if ($state === null || $state !== $request->query('state')) {
            return redirect()->route('dashboard')
                ->with('error', 'Strava-Verbindung konnte nicht abgeschlossen werden — bitte erneut versuchen.');
        }

        $this->client->exchangeCode($request->user(), $request->query('code'));

        $count = $importer->import($request->user());

        return redirect()->route('dashboard')
            ->with('status', "Strava verbunden — {$count} Aktivitäten importiert.");
    }
}
