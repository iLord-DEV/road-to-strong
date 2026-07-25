<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use App\Models\StrengthSession;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class MonthController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $from = now()->startOfMonth()->subMonths(11);

        $weightByMonth = BodyMeasurement::where('user_id', $user->id)
            ->whereNotNull('weight_kg')
            ->where('measured_at', '>=', $from)
            ->selectRaw("strftime('%Y-%m', measured_at) as ym, avg(weight_kg) as avg_weight")
            ->groupBy('ym')
            ->pluck('avg_weight', 'ym');

        $trainingByMonth = Activity::where('user_id', $user->id)
            ->where('started_at', '>=', $from)
            ->selectRaw("strftime('%Y-%m', started_at) as ym, sum(moving_time_s) as seconds, count(*) as sessions")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $strengthByMonth = StrengthSession::where('user_id', $user->id)
            ->where('performed_at', '>=', $from->toDateString())
            ->selectRaw("strftime('%Y-%m', performed_at) as ym, count(*) as sessions")
            ->groupBy('ym')
            ->pluck('sessions', 'ym');

        // One row per month, oldest first
        $months = collect(range(11, 0))->map(function (int $monthsAgo) use ($weightByMonth, $trainingByMonth, $strengthByMonth) {
            $month = now()->startOfMonth()->subMonths($monthsAgo);
            $ym = $month->format('Y-m');

            return [
                'month' => $month,
                'weight' => $weightByMonth->get($ym),
                'trainingSeconds' => (int) ($trainingByMonth->get($ym)->seconds ?? 0),
                'trainingSessions' => (int) ($trainingByMonth->get($ym)->sessions ?? 0),
                'strengthSessions' => (int) ($strengthByMonth->get($ym) ?? 0),
            ];
        });

        $logs = DailyLog::where('user_id', $user->id)
            ->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->get();

        return view('month', [
            'months' => $months,
            'current' => $months->last(),
            'previous' => $months->slice(-2, 1)->first(),
            'feierabendDays' => $logs->where('feierabend', true)->count(),
            'mittagDays' => $logs->where('mittag_vorbereitet', true)->count(),
            'naschenFreiDays' => $logs->where('naschen', 'keines')->count(),
            'schlafAvg' => $logs->whereNotNull('schlaf')->avg('schlaf'),
            'loggedDays' => $logs->count(),
        ]);
    }
}
