<div>
    <label for="recipe-name" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Name</label>
    <input
        id="recipe-name"
        name="name"
        type="text"
        required
        maxlength="100"
        value="{{ old('name', $recipe?->name) }}"
        class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
    >
    @error('name')
        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

<fieldset>
    <legend class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Rubrik</legend>
    <div class="mt-2 flex flex-wrap gap-4">
        @foreach (\App\Models\Recipe::CATEGORIES as $key => $label)
            <label class="flex min-h-11 items-center gap-2">
                <input
                    type="radio"
                    name="category"
                    value="{{ $key }}"
                    @checked(old('category', $recipe?->category ?? 'mittags') === $key)
                    class="size-4 accent-neutral-900 dark:accent-neutral-100"
                >
                <span>{{ $label }}</span>
            </label>
        @endforeach
    </div>
</fieldset>

<div>
    <label for="recipe-description" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Kurzbeschreibung (Zutaten)</label>
    <input
        id="recipe-description"
        name="description"
        type="text"
        maxlength="255"
        value="{{ old('description', $recipe?->description) }}"
        class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
    >
</div>

<div>
    <label for="recipe-instructions" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Zubereitung</label>
    <textarea
        id="recipe-instructions"
        name="instructions"
        rows="5"
        maxlength="5000"
        class="mt-2 block w-full rounded-xl border border-neutral-300 bg-white px-4 py-3 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
    >{{ old('instructions', $recipe?->instructions) }}</textarea>
</div>

<div>
    <label for="recipe-kcal" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Kalorien (ca.)</label>
    <input
        id="recipe-kcal"
        name="kcal"
        type="number"
        inputmode="numeric"
        min="0"
        max="2000"
        value="{{ old('kcal', $recipe?->kcal) }}"
        class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
    >
</div>
