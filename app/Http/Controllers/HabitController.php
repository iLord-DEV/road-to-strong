<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HabitController extends Controller
{
    // Nachtrags-Fenster: heute plus die letzten 3 Tage
    public const BACKFILL_DAYS = 3;

    public function backfill(Request $request, string $date): View
    {
        $day = $this->resolveDay($date);

        return view('backfill', [
            'day' => $day,
            'log' => DailyLog::firstWhere(['user_id' => $request->user()->id, 'date' => $day]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['required', Rule::in(array_keys(DailyLog::FIELDS))],
            'value' => ['required'],
            'date' => ['nullable', 'date'],
        ]);

        $day = isset($validated['date']) ? $this->resolveDay($validated['date']) : today();

        $field = $validated['field'];
        $value = $validated['value'];

        abort_unless(in_array($value, array_map('strval', DailyLog::FIELDS[$field]), true), 422);

        $log = DailyLog::firstOrCreate([
            'user_id' => $request->user()->id,
            'date' => $day,
        ]);

        // Tapping the already selected value clears it again
        $current = $log->getAttribute($field);
        if ($current !== null) {
            $currentString = is_bool($current) ? (string) (int) $current : (string) $current;
            if ($currentString === $value) {
                $value = null;
            }
        }

        $log->update([$field => $value]);

        // Jump back to the tapped habit group instead of the top of the page
        return back(fallback: route('dashboard'))->withFragment('habit-'.$field);
    }

    /**
     * Only today and the last BACKFILL_DAYS days are editable.
     */
    private function resolveDay(string $date): Carbon
    {
        try {
            $day = Carbon::parse($date)->startOfDay();
        } catch (\Throwable) {
            abort(404);
        }

        abort_if(
            $day->isFuture() || $day->lt(today()->subDays(self::BACKFILL_DAYS)),
            404,
        );

        return $day;
    }
}
