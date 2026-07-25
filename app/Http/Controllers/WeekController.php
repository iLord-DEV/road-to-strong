<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class WeekController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();
        $prevStart = $start->copy()->subWeek();
        $prevEnd = $start->copy()->subSecond();

        $weightAvg = fn (Carbon $from, Carbon $to): ?float => BodyMeasurement::where('user_id', $user->id)
            ->whereBetween('measured_at', [$from, $to])
            ->whereNotNull('weight_kg')
            ->avg('weight_kg');

        $trainingSeconds = fn (Carbon $from, Carbon $to): int => (int) Activity::where('user_id', $user->id)
            ->whereBetween('started_at', [$from, $to])
            ->sum('moving_time_s');

        // Weekly weight averages of the last 8 weeks for the sparkline
        $sparkline = collect(range(7, 0))
            ->map(function (int $weeksAgo) use ($weightAvg, $start) {
                $from = $start->copy()->subWeeks($weeksAgo);

                return $weightAvg($from, $from->copy()->endOfWeek());
            });

        $logs = DailyLog::where('user_id', $user->id)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->get();

        return view('week', [
            'start' => $start,
            'end' => $end,
            'weightThisWeek' => $weightAvg($start, $end),
            'weightLastWeek' => $weightAvg($prevStart, $prevEnd),
            'sparkline' => $sparkline,
            'trainingThisWeek' => $trainingSeconds($start, $end),
            'trainingLastWeek' => $trainingSeconds($prevStart, $prevEnd),
            'activityCount' => Activity::where('user_id', $user->id)->whereBetween('started_at', [$start, $end])->count(),
            'feierabendDays' => $logs->where('feierabend', true)->count(),
            'mittagDays' => $logs->where('mittag_vorbereitet', true)->count(),
            'naschenCounts' => $logs->whereNotNull('naschen')->countBy('naschen'),
            'schlafAvg' => $logs->whereNotNull('schlaf')->avg('schlaf'),
            'loggedDays' => $logs->count(),
        ]);
    }
}
