<x-layout title="Übungen — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="kraft" />
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Übungen</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">Dein Trainingsplan für Workout A und B.</p>
    </header>

    <div class="mt-8 space-y-4">
        @foreach (\App\Models\Exercise::WORKOUTS as $workout)
            <section aria-labelledby="exercises-{{ $workout }}-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <h2 id="exercises-{{ $workout }}-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                    Workout {{ $workout }}
                </h2>

                @if (($exercises[$workout] ?? collect())->isEmpty())
                    <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">Noch keine Übungen.</p>
                @else
                    <ul class="mt-3 divide-y divide-neutral-100 dark:divide-neutral-800">
                        @foreach ($exercises[$workout] as $exercise)
                            <li class="flex items-center justify-between gap-4 py-1">
                                <span>{{ $exercise->name }}</span>
                                <form
                                    method="POST"
                                    action="{{ route('exercises.destroy', $exercise) }}"
                                    onsubmit="return confirm('Übung „{{ $exercise->name }}“ entfernen? Bereits erfasste Trainings bleiben erhalten.');"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="flex min-h-11 items-center px-2 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
                                    >
                                        Entfernen
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        @endforeach

        <section aria-labelledby="add-exercise-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="add-exercise-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Übung hinzufügen
            </h2>
            <form method="POST" action="{{ route('exercises.store') }}" class="mt-4 space-y-4">
                @csrf
                <div>
                    <label for="name" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        required
                        maxlength="100"
                        value="{{ old('name') }}"
                        placeholder="z. B. Kniebeuge"
                        class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                    >
                    @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <fieldset>
                    <legend class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Workout</legend>
                    <div class="mt-2 flex gap-4">
                        @foreach (\App\Models\Exercise::WORKOUTS as $workout)
                            <label class="flex min-h-11 items-center gap-2">
                                <input
                                    type="radio"
                                    name="workout"
                                    value="{{ $workout }}"
                                    @checked(old('workout', 'A') === $workout)
                                    class="size-4 accent-neutral-900 dark:accent-neutral-100"
                                >
                                <span>{{ $workout }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <button
                    type="submit"
                    class="min-h-12 w-full rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                    Hinzufügen
                </button>
            </form>
        </section>

        <a
            href="{{ route('strength.index') }}"
            class="inline-flex min-h-11 items-center text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
        >
            ← Zurück zum Krafttraining
        </a>
    </div>
</x-layout>
