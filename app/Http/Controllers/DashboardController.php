<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $weight = $user->bodyMeasurements()->whereNotNull('weight_kg')->latest('measured_at')->first();

        // Trend: compare with the closest measurement at least 7 days older
        $weightTrend = null;
        if ($weight !== null) {
            $previous = $user->bodyMeasurements()
                ->whereNotNull('weight_kg')
                ->where('measured_at', '<=', $weight->measured_at->copy()->subDays(7))
                ->latest('measured_at')
                ->first();

            if ($previous !== null) {
                $weightTrend = [
                    'delta' => $weight->weight_kg - $previous->weight_kg,
                    'days' => (int) round($previous->measured_at->diffInDays($weight->measured_at)),
                ];
            }
        }

        return view('dashboard', [
            'weight' => $weight,
            'weightTrend' => $weightTrend,
            'activity' => $user->activities()->latest('started_at')->first(),
            'log' => DailyLog::firstWhere(['user_id' => $user->id, 'date' => today()]),
            'stravaConnected' => $user->oauthToken('strava') !== null,
            'withingsConnected' => $user->oauthToken('withings') !== null,
        ]);
    }
}
