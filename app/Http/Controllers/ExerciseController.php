<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ExerciseController extends Controller
{
    public function index(Request $request): View
    {
        $exercises = Exercise::where('user_id', $request->user()->id)
            ->orderBy('workout')
            ->orderBy('position')
            ->orderBy('id')
            ->get()
            ->groupBy('workout');

        return view('strength.exercises', ['exercises' => $exercises]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'workout' => ['required', Rule::in(Exercise::WORKOUTS)],
        ]);

        $position = Exercise::where('user_id', $request->user()->id)
            ->where('workout', $validated['workout'])
            ->max('position');

        Exercise::create([
            'user_id' => $request->user()->id,
            'name' => $validated['name'],
            'workout' => $validated['workout'],
            'position' => ($position ?? 0) + 1,
        ]);

        return redirect()->route('exercises.index');
    }

    public function destroy(Request $request, Exercise $exercise): RedirectResponse
    {
        abort_unless($exercise->user_id === $request->user()->id, 403);

        // Soft delete keeps logged history intact
        $exercise->delete();

        return redirect()->route('exercises.index');
    }
}
