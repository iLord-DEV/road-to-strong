<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use App\Models\FtpEntry;
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

        $activity = $user->activities()->latest('started_at')->first();

        // NP/kg is activity-bound: use the weight closest before the ride
        $activityWkg = null;
        if ($activity?->np_watts) {
            $weightAtActivity = $user->bodyMeasurements()
                ->whereNotNull('weight_kg')
                ->where('measured_at', '<=', $activity->started_at)
                ->latest('measured_at')
                ->first() ?? $weight;

            if ($weightAtActivity !== null) {
                $activityWkg = $activity->np_watts / $weightAtActivity->weight_kg;
            }
        }

        $ftp = FtpEntry::where('user_id', $user->id)->orderByDesc('tested_at')->first();

        return view('dashboard', [
            'weight' => $weight,
            'weightTrend' => $weightTrend,
            'activity' => $activity,
            'activityWkg' => $activityWkg,
            'ftp' => $ftp,
            'ftpWkg' => ($ftp !== null && $weight !== null) ? $ftp->watts / $weight->weight_kg : null,
            'log' => DailyLog::firstWhere(['user_id' => $user->id, 'date' => today()]),
            'stravaConnected' => $user->oauthToken('strava') !== null,
            'withingsConnected' => $user->oauthToken('withings') !== null,
        ]);
    }
}
