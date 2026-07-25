<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        return view('dashboard', [
            'weight' => $user->bodyMeasurements()->whereNotNull('weight_kg')->latest('measured_at')->first(),
            'activity' => $user->activities()->latest('started_at')->first(),
            'stravaConnected' => $user->oauthToken('strava') !== null,
            'withingsConnected' => $user->oauthToken('withings') !== null,
        ]);
    }
}
