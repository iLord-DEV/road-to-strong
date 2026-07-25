<?php

namespace App\Http\Controllers;

use App\Models\DailyLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HabitController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'field' => ['required', Rule::in(array_keys(DailyLog::FIELDS))],
            'value' => ['required'],
        ]);

        $field = $validated['field'];
        $value = $validated['value'];

        abort_unless(in_array($value, array_map('strval', DailyLog::FIELDS[$field]), true), 422);

        $log = DailyLog::firstOrCreate([
            'user_id' => $request->user()->id,
            'date' => today(),
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

        return redirect()->route('dashboard');
    }
}
