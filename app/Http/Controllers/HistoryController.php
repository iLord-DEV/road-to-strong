<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\BodyMeasurement;
use App\Models\DailyLog;
use App\Models\FtpEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HistoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        $range = $request->query('zeitraum');
        if (! in_array($range, ['6m', '1j', 'alles'], true)) {
            $range = '1j';
        }

        $from = match ($range) {
            '6m' => now()->subMonths(6),
            '1j' => now()->subYear(),
            'alles' => null,
        };

        $measurements = BodyMeasurement::where('user_id', $user->id)
            ->whereNotNull('weight_kg')
            ->when($from, fn ($q) => $q->where('measured_at', '>=', $from))
            ->orderBy('measured_at')
            ->get(['measured_at', 'weight_kg', 'fat_percent', 'muscle_mass_kg']);

        $weightDaily = $this->dailyAverages($measurements, 'weight_kg');

        // Long ranges show the trend only — no daily noise (see VISION.md)
        $weightSeries = [
            ['values' => $this->rollingMean($weightDaily), 'class' => 'text-neutral-900 dark:text-neutral-100', 'width' => 1.8],
        ];
        if ($range !== 'alles') {
            array_unshift($weightSeries, ['values' => $weightDaily, 'class' => 'text-neutral-300 dark:text-neutral-700', 'width' => 1]);
        }

        return view('history', [
            'range' => $range,
            'weightChart' => $this->lineChart($weightSeries, 'kg'),
            'fatChart' => $this->lineChart([
                ['values' => $this->dailyAverages($measurements, 'fat_percent'), 'class' => 'text-neutral-900 dark:text-neutral-100', 'width' => 1.8],
            ], '%'),
            'muscleChart' => $this->lineChart([
                ['values' => $this->dailyAverages($measurements, 'muscle_mass_kg'), 'class' => 'text-neutral-900 dark:text-neutral-100', 'width' => 1.8],
            ], 'kg'),
            'training' => $this->trainingByMonth($user->id, $from),
            'sleepChart' => $this->sleepEnergyChart($user->id, $from),
            'habitChart' => $this->habitQuotaChart($user->id, $from),
            'ftpEntries' => FtpEntry::where('user_id', $user->id)->orderByDesc('tested_at')->get(),
        ]);
    }

    /**
     * Weekly averages of the 1-5 scales: sleep (strong) and energy (light).
     */
    private function sleepEnergyChart(int $userId, ?Carbon $from): ?array
    {
        $byWeek = $this->logsByWeek($userId, $from);

        return $this->lineChart([
            [
                'values' => $byWeek->map(fn ($logs) => $logs->whereNotNull('schlaf')->avg('schlaf'))
                    ->filter(fn ($v) => $v !== null)->map(fn ($v) => round($v, 2)),
                'class' => 'text-neutral-900 dark:text-neutral-100',
                'width' => 1.8,
            ],
            [
                'values' => $byWeek->map(fn ($logs) => $logs->whereNotNull('energie')->avg('energie'))
                    ->filter(fn ($v) => $v !== null)->map(fn ($v) => round($v, 2)),
                'class' => 'text-neutral-400 dark:text-neutral-600',
                'width' => 1.2,
            ],
        ], 'Ø');
    }

    /**
     * Weekly adherence quotas: Feierabend (strong) and Mittag (light),
     * as percentage of days that were actually logged.
     */
    private function habitQuotaChart(int $userId, ?Carbon $from): ?array
    {
        $byWeek = $this->logsByWeek($userId, $from);

        $quota = fn ($logs, string $field) => ($logged = $logs->whereNotNull($field))->isEmpty()
            ? null
            : round($logged->filter(fn ($log) => $log->{$field})->count() / $logged->count() * 100);

        return $this->lineChart([
            [
                'values' => $byWeek->map(fn ($logs) => $quota($logs, 'feierabend'))->filter(fn ($v) => $v !== null),
                'class' => 'text-neutral-900 dark:text-neutral-100',
                'width' => 1.8,
            ],
            [
                'values' => $byWeek->map(fn ($logs) => $quota($logs, 'mittag_vorbereitet'))->filter(fn ($v) => $v !== null),
                'class' => 'text-neutral-400 dark:text-neutral-600',
                'width' => 1.2,
            ],
        ], '%');
    }

    /**
     * @return Collection<string, Collection<int, DailyLog>>
     */
    private function logsByWeek(int $userId, ?Carbon $from): Collection
    {
        return DailyLog::where('user_id', $userId)
            ->when($from, fn ($q) => $q->where('date', '>=', $from->toDateString()))
            ->orderBy('date')
            ->get()
            ->groupBy(fn (DailyLog $log) => $log->date->copy()->startOfWeek()->toDateString());
    }

    /**
     * Scale glitches (wrong person, half-stepped measurement) stay in the raw
     * data but are excluded from evaluations.
     */
    private const PLAUSIBLE = [
        'weight_kg' => [40, 150],
        'fat_percent' => [5, 50],
        'muscle_mass_kg' => [20, 90],
    ];

    /**
     * Average per calendar day, keyed by Y-m-d, oldest first.
     *
     * @return Collection<string, float>
     */
    private function dailyAverages(Collection $measurements, string $field): Collection
    {
        [$min, $max] = self::PLAUSIBLE[$field];

        return $measurements
            ->filter(fn ($m) => $m->{$field} !== null && $m->{$field} >= $min && $m->{$field} <= $max)
            ->groupBy(fn ($m) => $m->measured_at->toDateString())
            ->map(fn ($group) => round($group->avg($field), 2));
    }

    /**
     * Rolling 7-day mean over daily values (calendar window).
     *
     * @param  Collection<string, float>  $daily
     * @return Collection<string, float>
     */
    private function rollingMean(Collection $daily): Collection
    {
        return $daily->mapWithKeys(function (float $value, string $date) use ($daily) {
            $day = Carbon::parse($date);

            $window = collect(range(0, 6))
                ->map(fn (int $i) => $daily->get($day->copy()->subDays($i)->toDateString()))
                ->filter(fn ($v) => $v !== null);

            return [$date => round($window->avg(), 2)];
        });
    }

    /**
     * Build polyline coordinates (viewBox 0 0 100 40) for one or more series
     * sharing the same time and value domain.
     *
     * @param  array<int, array{values: Collection<string, float>, class: string, width: float}>  $series
     * @return array{series: array, min: float, max: float, from: ?Carbon, to: ?Carbon, latest: ?float, unit: string}|null
     */
    private function lineChart(array $series, string $unit): ?array
    {
        $all = collect($series)->flatMap(fn (array $s) => $s['values']);

        if ($all->count() < 2) {
            return null;
        }

        $dates = collect($series)->flatMap(fn (array $s) => $s['values']->keys());
        $fromTs = Carbon::parse($dates->min())->timestamp;
        $toTs = Carbon::parse($dates->max())->timestamp;
        $min = $all->min();
        $max = $all->max();
        $valueRange = max($max - $min, 0.1);
        $timeRange = max($toTs - $fromTs, 1);

        $built = collect($series)
            ->filter(fn (array $s) => $s['values']->count() >= 2)
            ->map(function (array $s) use ($fromTs, $timeRange, $min, $valueRange) {
                $points = $s['values']
                    ->map(function (float $v, string $date) use ($fromTs, $timeRange, $min, $valueRange) {
                        $x = (Carbon::parse($date)->timestamp - $fromTs) / $timeRange * 100;
                        $y = 2 + (1 - ($v - $min) / $valueRange) * 36;

                        return round($x, 2).','.round($y, 2);
                    })
                    ->implode(' ');

                return ['points' => $points, 'class' => $s['class'], 'width' => $s['width']];
            })
            ->values()
            ->all();

        return [
            'series' => $built,
            'min' => $min,
            'max' => $max,
            'from' => Carbon::createFromTimestamp($fromTs, config('app.timezone')),
            'to' => Carbon::createFromTimestamp($toTs, config('app.timezone')),
            'latest' => end($series)['values']->last(),
            'unit' => $unit,
        ];
    }

    /**
     * Monthly training bars plus range totals.
     *
     * @return array{bars: array, maxHours: float, totals: array{hours: int, km: int, hm: int, kj: int}}|null
     */
    private function trainingByMonth(int $userId, ?Carbon $from): ?array
    {
        $rows = Activity::where('user_id', $userId)
            ->when($from, fn ($q) => $q->where('started_at', '>=', $from))
            ->selectRaw("strftime('%Y-%m', started_at) as ym,
                sum(moving_time_s) as seconds,
                sum(distance_m) as meters,
                sum(elevation_gain_m) as climb,
                sum(kilojoules) as kj")
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        if ($rows->isEmpty()) {
            return null;
        }

        // Continuous month axis so training gaps stay visible
        $first = Carbon::parse($rows->first()->ym.'-01');
        $months = (int) $first->diffInMonths(now()->startOfMonth()) + 1;
        $byYm = $rows->keyBy('ym');
        $maxHours = max($rows->max('seconds') / 3600, 1);

        $slot = 100 / $months;
        $bars = collect(range(0, $months - 1))->map(function (int $i) use ($first, $byYm, $maxHours, $slot) {
            $ym = $first->copy()->addMonths($i)->format('Y-m');
            $hours = ($byYm->get($ym)->seconds ?? 0) / 3600;
            $height = round($hours / $maxHours * 36, 2);

            return [
                'x' => round($i * $slot + $slot * 0.15, 2),
                'w' => round($slot * 0.7, 2),
                'y' => round(38 - $height, 2),
                'h' => $height,
            ];
        })->all();

        return [
            'bars' => $bars,
            'maxHours' => round($maxHours, 1),
            'from' => $first,
            'totals' => [
                'hours' => (int) round($rows->sum('seconds') / 3600),
                'km' => (int) round($rows->sum('meters') / 1000),
                'hm' => (int) round($rows->sum('climb')),
                'kj' => (int) round($rows->sum('kj')),
            ],
        ];
    }
}
