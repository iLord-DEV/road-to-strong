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
        $validated = $this->validated($request);

        $position = Exercise::where('user_id', $request->user()->id)
            ->where('workout', $validated['workout'])
            ->max('position');

        Exercise::create([
            ...$validated,
            'user_id' => $request->user()->id,
            'position' => ($position ?? 0) + 1,
        ]);

        return redirect()->route('exercises.index');
    }

    public function edit(Request $request, Exercise $exercise): View
    {
        abort_unless($exercise->user_id === $request->user()->id, 403);

        return view('strength.exercise-edit', ['exercise' => $exercise]);
    }

    public function update(Request $request, Exercise $exercise): RedirectResponse
    {
        abort_unless($exercise->user_id === $request->user()->id, 403);

        $exercise->update($this->validated($request));

        return redirect()->route('exercises.index');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'workout' => ['required', Rule::in(Exercise::WORKOUTS)],
            'video_url' => ['nullable', 'url:http,https', 'max:255'],
        ]);
    }

    public function destroy(Request $request, Exercise $exercise): RedirectResponse
    {
        abort_unless($exercise->user_id === $request->user()->id, 403);

        // Soft delete keeps logged history intact
        $exercise->delete();

        return redirect()->route('exercises.index');
    }
}
