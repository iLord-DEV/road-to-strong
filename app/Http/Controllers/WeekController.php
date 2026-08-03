<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use App\Models\StrengthSession;
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
            ->get()
            ->keyBy(fn (DailyLog $log) => $log->date->toDateString());

        return view('week', [
            'start' => $start,
            'end' => $end,
            'weightThisWeek' => $weightAvg($start, $end),
            'weightLastWeek' => $weightAvg($prevStart, $prevEnd),
            'sparkline' => $sparkline,
            'trainingThisWeek' => $trainingSeconds($start, $end),
            'trainingLastWeek' => $trainingSeconds($prevStart, $prevEnd),
            'activityCount' => Activity::where('user_id', $user->id)->whereBetween('started_at', [$start, $end])->count(),
            'strengthCount' => StrengthSession::where('user_id', $user->id)
                ->whereBetween('performed_at', [$start->toDateString(), $end->toDateString()])
                ->count(),
            'habitRows' => $this->habitRows($start, $logs),
            'schlafAvg' => $logs->whereNotNull('schlaf')->avg('schlaf'),
            'cravingAvg' => $logs->whereNotNull('craving')->avg('craving'),
            'loggedDays' => $logs->count(),
        ]);
    }

    /**
     * One dot row per habit, seven dots Mo-So.
     * States: ja | nein | half | leer (nicht erfasst) | na | future
     *
     * @param  \Illuminate\Support\Collection<string, DailyLog>  $logs
     * @return array<int, array{label: string, dots: array}>
     */
    private function habitRows(Carbon $start, $logs): array
    {
        $days = collect(range(0, 6))->map(fn (int $i) => $start->copy()->addDays($i));

        $boolDots = function (string $field, bool $workdaysOnly = false) use ($days, $logs): array {
            return $days->map(function (Carbon $day) use ($field, $workdaysOnly, $logs) {
                if ($workdaysOnly && $day->isWeekend()) {
                    return ['state' => 'na', 'aria' => $day->isoFormat('dd').': entfällt'];
                }
                if ($day->isFuture()) {
                    return ['state' => 'future', 'aria' => $day->isoFormat('dd').': noch offen'];
                }

                $value = $logs->get($day->toDateString())?->{$field};
                $state = $value === null ? 'leer' : ($value ? 'ja' : 'nein');
                $text = $value === null ? 'nicht erfasst' : ($value ? 'Ja' : 'Nein');

                return ['state' => $state, 'aria' => $day->isoFormat('dd').': '.$text];
            })->all();
        };

        $naschenDots = $days->map(function (Carbon $day) use ($logs) {
            if ($day->isFuture()) {
                return ['state' => 'future', 'aria' => $day->isoFormat('dd').': noch offen'];
            }

            $value = $logs->get($day->toDateString())?->naschen;
            $state = match ($value) {
                'keines' => 'ja',
                'bewusst' => 'half',
                'automatisch' => 'nein',
                default => 'leer',
            };

            return ['state' => $state, 'aria' => $day->isoFormat('dd').': '.($value ?? 'nicht erfasst')];
        })->all();

        return [
            ['label' => 'Durchgeschlafen', 'dots' => $boolDots('durchgeschlafen')],
            ['label' => 'Schlafhygiene', 'dots' => $boolDots('schlafhygiene')],
            ['label' => 'Mittag vorbereitet', 'dots' => $boolDots('mittag_vorbereitet', workdaysOnly: true)],
            ['label' => 'Feierabend eingehalten', 'dots' => $boolDots('feierabend')],
            ['label' => 'Naschen', 'dots' => $naschenDots],
            ['label' => 'Cannabis am Vortag', 'dots' => $boolDots('cannabis_vortag')],
        ];
    }
}
