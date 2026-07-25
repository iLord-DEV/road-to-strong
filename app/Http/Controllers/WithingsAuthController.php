<?php

namespace App\Http\Controllers;

use App\Modules\Withings\MeasurementImporter;
use App\Modules\Withings\WithingsClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithingsAuthController extends Controller
{
    public function __construct(
        private readonly WithingsClient $client,
    ) {}

    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('oauth_state.withings', $state);

        return redirect()->away(
            $this->client->authorizeUrl(route('withings.callback'), $state),
        );
    }

    public function callback(Request $request, MeasurementImporter $importer): RedirectResponse
    {
        $state = $request->session()->pull('oauth_state.withings');

        if ($request->query('error') !== null) {
            return redirect()->route('dashboard')->with('error', 'Withings-Verbindung abgebrochen.');
        }

        // A duplicate callback (reload, double redirect) finds no state in the
        // session because it was pulled on the first call — don't hard-fail.
        if ($state === null || $state !== $request->query('state')) {
            return redirect()->route('dashboard')
                ->with('error', 'Withings-Verbindung konnte nicht abgeschlossen werden — bitte erneut versuchen.');
        }

        $this->client->exchangeCode($request->user(), $request->query('code'), route('withings.callback'));

        $count = $importer->import($request->user());

        return redirect()->route('dashboard')
            ->with('status', "Withings verbunden — {$count} Messungen importiert.");
    }
}
