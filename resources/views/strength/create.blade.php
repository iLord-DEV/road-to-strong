<x-layout title="Workout {{ $workout }} — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="kraft" />
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Workout {{ $workout }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">Letzte Werte sind vorausgefüllt — steigere dich, wenn es geht.</p>
    </header>

    @if ($errors->any())
        <p class="mt-6 rounded-xl bg-white p-4 text-sm text-red-600 shadow-sm dark:bg-neutral-900 dark:text-red-400" role="alert">
            {{ $errors->first() }}
        </p>
    @endif

    <form method="POST" action="{{ route('strength.store') }}" class="mt-8 space-y-4">
        @csrf
        <input type="hidden" name="workout" value="{{ $workout }}">

        <div class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <label for="performed_at" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Datum</label>
            <input
                id="performed_at"
                name="performed_at"
                type="date"
                required
                value="{{ old('performed_at', today()->toDateString()) }}"
                max="{{ today()->toDateString() }}"
                class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
            >
        </div>

        @foreach ($exercises as $index => $item)
            @php
                $exercise = $item['exercise'];
                $last = $item['last'];
            @endphp
            <div role="group" aria-labelledby="exercise-{{ $exercise->id }}-name" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <div class="flex items-baseline justify-between gap-4">
                    <p id="exercise-{{ $exercise->id }}-name" class="font-medium">
                        {{ $exercise->name }}
                        @if ($exercise->video_url)
                            <a
                                href="{{ $exercise->video_url }}"
                                target="_blank"
                                rel="noopener"
                                class="ml-1 text-sm font-normal text-neutral-400 underline-offset-4 hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
                            >Video ↗</a>
                        @endif
                    </p>
                    @if ($last)
                        <p class="text-xs text-neutral-400 tabular-nums dark:text-neutral-500">Letztes Mal: {{ $last->summary() }}</p>
                    @endif
                </div>

                <input type="hidden" name="entries[{{ $index }}][exercise_id]" value="{{ $exercise->id }}">

                <div class="mt-4 grid grid-cols-3 gap-3">
                    <div>
                        <label for="weight-{{ $exercise->id }}" class="block text-xs text-neutral-500 dark:text-neutral-400">Gewicht (kg)</label>
                        <input
                            id="weight-{{ $exercise->id }}"
                            name="entries[{{ $index }}][weight_kg]"
                            type="number"
                            inputmode="decimal"
                            step="0.5"
                            min="0"
                            max="500"
                            value="{{ old("entries.$index.weight_kg", $last?->weight_kg) }}"
                            class="mt-1 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-3 text-base tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                        >
                    </div>
                    <div>
                        <label for="reps-{{ $exercise->id }}" class="block text-xs text-neutral-500 dark:text-neutral-400">Wdh.</label>
                        <input
                            id="reps-{{ $exercise->id }}"
                            name="entries[{{ $index }}][reps]"
                            type="number"
                            inputmode="numeric"
                            min="1"
                            max="100"
                            value="{{ old("entries.$index.reps", $last?->reps) }}"
                            class="mt-1 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-3 text-base tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                        >
                    </div>
                    <div>
                        <label for="sets-{{ $exercise->id }}" class="block text-xs text-neutral-500 dark:text-neutral-400">Sätze</label>
                        <input
                            id="sets-{{ $exercise->id }}"
                            name="entries[{{ $index }}][sets]"
                            type="number"
                            inputmode="numeric"
                            min="1"
                            max="20"
                            value="{{ old("entries.$index.sets", $last?->sets) }}"
                            class="mt-1 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-3 text-base tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                        >
                    </div>
                </div>
            </div>
        @endforeach

        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="min-h-12 flex-1 rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
            >
                Speichern
            </button>
            <a
                href="{{ route('strength.index') }}"
                class="flex min-h-12 items-center px-4 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
            >
                Abbrechen
            </a>
        </div>
    </form>
</x-layout>
