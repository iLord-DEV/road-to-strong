<x-layout title="{{ $recipe->name }} — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="rezepte" />
    </div>

    <header class="mt-4">
        <p class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
            {{ \App\Models\Recipe::CATEGORIES[$recipe->category] ?? $recipe->category }}
        </p>
        <h1 class="mt-1 text-3xl font-semibold tracking-tight">{{ $recipe->name }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">
            @if ($recipe->description){{ $recipe->description }}@endif
            @if ($recipe->kcal) · ~{{ $recipe->kcal }} kcal @endif
        </p>
    </header>

    <div class="mt-8 space-y-4">
        @if ($recipe->instructions)
            <section aria-labelledby="instructions-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <h2 id="instructions-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                    Zubereitung
                </h2>
                <div class="mt-3 space-y-2 text-neutral-700 dark:text-neutral-300">
                    @foreach (preg_split('/\r\n|\r|\n/', $recipe->instructions) as $line)
                        @if (trim($line) !== '')
                            <p>{{ $line }}</p>
                        @endif
                    @endforeach
                </div>
            </section>
        @endif

        <section aria-labelledby="rating-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="rating-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Bewertung
            </h2>
            <div class="mt-3 space-y-0.5">
                <x-star-rating :recipe="$recipe" dimension="geschmack" label="Geschmack" :current="$recipe->stars_geschmack" />
                <x-star-rating :recipe="$recipe" dimension="aufwand" label="Aufwand" :current="$recipe->stars_aufwand" />
                <x-star-rating :recipe="$recipe" dimension="kalorien" label="Kalorien" :current="$recipe->stars_kalorien" />
            </div>
            <p class="mt-2 text-xs text-neutral-400 dark:text-neutral-500">
                Aufwand: 1 = kaum Aufwand · Kalorien: 5 = sehr leicht · erneutes Tippen löscht
            </p>
        </section>

        <div class="flex items-center gap-4">
            <a
                href="{{ route('recipes.edit', $recipe) }}"
                class="inline-flex min-h-12 flex-1 items-center justify-center rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
            >
                Bearbeiten
            </a>
            <form method="POST" action="{{ route('recipes.destroy', $recipe) }}" onsubmit="return confirm('Rezept „{{ $recipe->name }}“ löschen?');">
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="flex min-h-12 items-center px-4 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
                >
                    Löschen
                </button>
            </form>
        </div>

        <a
            href="{{ route('recipes.index') }}"
            class="inline-flex min-h-11 items-center text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
        >
            ← Alle Rezepte
        </a>
    </div>
</x-layout>
