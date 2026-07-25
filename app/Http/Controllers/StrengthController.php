<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\StrengthSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StrengthController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $workouts = collect(Exercise::WORKOUTS)->mapWithKeys(function (string $workout) use ($user) {
            $exercises = Exercise::where('user_id', $user->id)
                ->where('workout', $workout)
                ->orderBy('position')
                ->orderBy('id')
                ->get();

            $lastSession = StrengthSession::where('user_id', $user->id)
                ->where('workout', $workout)
                ->latest('performed_at')
                ->first();

            return [$workout => [
                'exercises' => $exercises,
                'lastSession' => $lastSession,
            ]];
        });

        return view('strength.index', ['workouts' => $workouts]);
    }

    public function create(Request $request): View
    {
        $workout = $request->query('workout');
        abort_unless(in_array($workout, Exercise::WORKOUTS, true), 404);

        $exercises = Exercise::where('user_id', $request->user()->id)
            ->where('workout', $workout)
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->map(fn (Exercise $exercise) => [
                'exercise' => $exercise,
                'last' => $exercise->lastEntry(),
            ]);

        abort_if($exercises->isEmpty(), 404);

        return view('strength.create', [
            'workout' => $workout,
            'exercises' => $exercises,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'workout' => ['required', Rule::in(Exercise::WORKOUTS)],
            'performed_at' => ['required', 'date', 'before_or_equal:today'],
            'entries' => ['required', 'array'],
            'entries.*.exercise_id' => [
                'required',
                Rule::exists('exercises', 'id')->where('user_id', $user->id)->whereNull('deleted_at'),
            ],
            'entries.*.weight_kg' => ['nullable', 'numeric', 'min:0', 'max:500'],
            'entries.*.reps' => ['nullable', 'integer', 'min:1', 'max:100'],
            'entries.*.sets' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        // Only exercises with reps and sets count as performed
        $entries = collect($validated['entries'])
            ->filter(fn (array $entry) => ($entry['reps'] ?? null) !== null && ($entry['sets'] ?? null) !== null);

        if ($entries->isEmpty()) {
            return back()->withInput()->withErrors([
                'entries' => 'Trag für mindestens eine Übung Wiederholungen und Sätze ein.',
            ]);
        }

        DB::transaction(function () use ($user, $validated, $entries) {
            $session = StrengthSession::create([
                'user_id' => $user->id,
                'workout' => $validated['workout'],
                'performed_at' => $validated['performed_at'],
            ]);

            foreach ($entries as $entry) {
                $session->entries()->create([
                    'exercise_id' => $entry['exercise_id'],
                    'weight_kg' => $entry['weight_kg'] ?? null,
                    'reps' => $entry['reps'],
                    'sets' => $entry['sets'],
                ]);
            }
        });

        return redirect()->route('strength.index')
            ->with('status', 'Workout '.$validated['workout'].' gespeichert.');
    }
}
