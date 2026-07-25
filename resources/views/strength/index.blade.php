<x-layout title="Kraft — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="kraft" />
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

    <header class="mt-4 flex items-start justify-between">
        <h1 class="text-3xl font-semibold tracking-tight">Krafttraining</h1>
        <a
            href="{{ route('exercises.index') }}"
            class="flex min-h-11 items-center text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
        >
            Übungen bearbeiten
        </a>
    </header>

    @if (session('status'))
        <p class="mt-6 rounded-xl bg-white p-4 text-sm text-neutral-700 shadow-sm dark:bg-neutral-900 dark:text-neutral-300" role="status">
            {{ session('status') }}
        </p>
    @endif

    <div class="mt-8 space-y-4">
        @foreach ($workouts as $workout => $data)
            <section aria-labelledby="workout-{{ $workout }}-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <div class="flex items-baseline justify-between">
                    <h2 id="workout-{{ $workout }}-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                        Workout {{ $workout }}
                    </h2>
                    @if ($data['lastSession'])
                        <p class="text-xs text-neutral-400 dark:text-neutral-500">
                            Zuletzt
                            @if ($data['lastSession']->performed_at->isToday())
                                heute
                            @elseif ($data['lastSession']->performed_at->isYesterday())
                                gestern
                            @else
                                am {{ $data['lastSession']->performed_at->isoFormat('D. MMM') }}
                            @endif
                        </p>
                    @endif
                </div>

                @if ($data['exercises']->isEmpty())
                    <p class="mt-3 text-neutral-500 dark:text-neutral-400">
                        Noch keine Übungen angelegt.
                    </p>
                    <a
                        href="{{ route('exercises.index') }}"
                        class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-neutral-900 px-5 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                    >
                        Übungen anlegen
                    </a>
                @else
                    <ul class="mt-4 space-y-2">
                        @foreach ($data['exercises'] as $exercise)
                            @php $last = $exercise->lastEntry(); @endphp
                            <li class="flex items-baseline justify-between gap-4">
                                <span>{{ $exercise->name }}</span>
                                <span class="text-sm text-neutral-500 tabular-nums dark:text-neutral-400">{{ $last?->summary() ?? '—' }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <a
                        href="{{ route('strength.create', ['workout' => $workout]) }}"
                        class="mt-5 inline-flex min-h-11 items-center rounded-xl bg-neutral-900 px-5 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                    >
                        Workout {{ $workout }} eintragen
                    </a>
                @endif
            </section>
        @endforeach
    </div>
</x-layout>
