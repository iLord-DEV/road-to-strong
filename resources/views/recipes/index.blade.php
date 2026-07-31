<x-layout title="Rezepte — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="rezepte" />
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex min-h-11 items-center rounded-lg px-2 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
            >
                Abmelden
            </button>
        </form>
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Rezepte</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">Leicht, lecker, vorbereitbar — deine Sammlung.</p>
    </header>

    <div class="mt-8 space-y-4">
        @foreach (\App\Models\Recipe::CATEGORIES as $key => $label)
            <section aria-labelledby="recipes-{{ $key }}-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <h2 id="recipes-{{ $key }}-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                    {{ $label }}
                </h2>

                @if (($recipes[$key] ?? collect())->isEmpty())
                    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">Noch keine Rezepte.</p>
                @else
                    <ul class="mt-2 divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach ($recipes[$key] as $recipe)
                            <li>
                                <a
                                    href="{{ route('recipes.show', $recipe) }}"
                                    class="block min-h-11 py-3 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100"
                                >
                                    <span class="flex items-baseline justify-between gap-4">
                                        <span class="font-medium">{{ $recipe->name }}</span>
                                        <span class="shrink-0 text-sm text-neutral-500 tabular-nums dark:text-neutral-400">
                                            @if ($recipe->kcal)~{{ $recipe->kcal }} kcal @endif
                                        </span>
                                    </span>
                                    @if ($recipe->description)
                                        <span class="mt-0.5 block text-sm text-neutral-500 dark:text-neutral-400">{{ $recipe->description }}</span>
                                    @endif
                                    <span class="mt-1 block text-sm text-neutral-400 tabular-nums dark:text-neutral-500" aria-hidden="true">
                                        @if ($recipe->stars_geschmack)Geschmack {{ str_repeat('★', $recipe->stars_geschmack) }} · @endif
                                        Aufwand {{ str_repeat('★', $recipe->stars_aufwand ?? 0) ?: '—' }}
                                        · Kalorien {{ str_repeat('★', $recipe->stars_kalorien ?? 0) ?: '—' }}
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach

        {{-- Neues Rezept --}}
        <section aria-labelledby="add-recipe-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="add-recipe-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Rezept hinzufügen
            </h2>
            <form method="POST" action="{{ route('recipes.store') }}" class="mt-4 space-y-4">
                @csrf
                @include('recipes.partials.fields', ['recipe' => null])
                <button
                    type="submit"
                    class="min-h-12 w-full rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                    Hinzufügen
                </button>
            </form>
        </section>

        <p class="text-xs text-neutral-400 dark:text-neutral-500">
            Sterne: Geschmack und Aufwand nach deinem Empfinden (Aufwand: 1 = kaum Aufwand). Kalorien: 5 = sehr leicht, bewertet relativ zur Rubrik. Bewerten und Bearbeiten direkt im Rezept.
        </p>
    </div>
</x-layout>
