<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RecipeController extends Controller
{
    public function index(Request $request): View
    {
        $recipes = Recipe::where('user_id', $request->user()->id)
            ->orderBy('name')
            ->get()
            ->groupBy('category');

        return view('recipes.index', ['recipes' => $recipes]);
    }

    public function show(Request $request, Recipe $recipe): View
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        return view('recipes.show', ['recipe' => $recipe]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        $recipe = Recipe::create([...$validated, 'user_id' => $request->user()->id]);

        return redirect()->route('recipes.show', $recipe);
    }

    public function edit(Request $request, Recipe $recipe): View
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        return view('recipes.edit', ['recipe' => $recipe]);
    }

    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $recipe->update($this->validated($request));

        return redirect()->route('recipes.show', $recipe);
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'category' => ['required', Rule::in(array_keys(Recipe::CATEGORIES))],
            'description' => ['nullable', 'string', 'max:255'],
            'instructions' => ['nullable', 'string', 'max:5000'],
            'kcal' => ['nullable', 'integer', 'min:0', 'max:2000'],
        ]);
    }

    public function rate(Request $request, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'dimension' => ['required', Rule::in(array_keys(Recipe::RATINGS))],
            'stars' => ['required', 'integer', 'min:1', 'max:5'],
        ]);

        $field = 'stars_'.$validated['dimension'];

        // Tapping the current rating clears it again
        $recipe->update([
            $field => $recipe->{$field} === (int) $validated['stars'] ? null : $validated['stars'],
        ]);

        return back();
    }

    public function destroy(Request $request, Recipe $recipe): RedirectResponse
    {
        abort_unless($recipe->user_id === $request->user()->id, 403);

        $recipe->delete();

        return redirect()->route('recipes.index');
    }
}
