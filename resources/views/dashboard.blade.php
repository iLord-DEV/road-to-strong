<x-layout>
    <header class="flex items-start justify-between">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">Heute</h1>
            <p class="mt-1 text-neutral-500 dark:text-neutral-400">{{ now()->isoFormat('dddd, D. MMMM') }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="flex min-h-11 min-w-11 items-center justify-center rounded-lg px-2 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
            >
                Abmelden
            </button>
        </form>
    </header>

    @if (session('status'))
        <p class="mt-6 rounded-xl bg-white p-4 text-sm text-neutral-700 shadow-sm dark:bg-neutral-900 dark:text-neutral-300" role="status">
            {{ session('status') }}
        </p>
    @endif

    @if (session('error'))
        <p class="mt-6 rounded-xl bg-white p-4 text-sm text-red-600 shadow-sm dark:bg-neutral-900 dark:text-red-400" role="alert">
            {{ session('error') }}
        </p>
    @endif

    <div class="mt-8 space-y-4">

        {{-- Gewicht --}}
        <section aria-labelledby="weight-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="weight-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Gewicht
            </h2>
            @if ($weight)
                <p class="mt-3 text-5xl font-semibold tracking-tight tabular-nums">
                    {{ number_format($weight->weight_kg, 1, ',', '.') }}<span class="ml-1 text-2xl font-normal text-neutral-400">kg</span>
                </p>
                <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                    @if ($weight->measured_at->isToday())
                        Heute, {{ $weight->measured_at->format('H:i') }} Uhr
                    @elseif ($weight->measured_at->isYesterday())
                        Gestern
                    @else
                        {{ $weight->measured_at->isoFormat('dd, D. MMMM') }}
                    @endif
                </p>
            @elseif ($withingsConnected)
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Noch keine Messung vorhanden.</p>
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Verbinde Withings, um dein Gewicht automatisch zu importieren.</p>
                <a
                    href="{{ route('withings.redirect') }}"
                    class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-neutral-900 px-5 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                    Withings verbinden
                </a>
            @endif
        </section>

        {{-- Training --}}
        <section aria-labelledby="training-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="training-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Letzte Aktivität
            </h2>
            @if ($activity)
                <p class="mt-3 text-2xl font-semibold tracking-tight">
                    {{ $activity->sportLabel() }}
                </p>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">
                    @if ($activity->started_at->isToday())
                        Heute
                    @elseif ($activity->started_at->isYesterday())
                        Gestern
                    @else
                        {{ $activity->started_at->isoFormat('dd, D. MMMM') }}
                    @endif
                    · {{ $activity->name }}
                </p>
                <dl class="mt-4 grid grid-cols-3 gap-4">
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">Dauer</dt>
                        <dd class="mt-1 text-lg font-medium tabular-nums">{{ $activity->durationFormatted() }}</dd>
                    </div>
                    @if ($activity->distance_m > 0)
                        <div>
                            <dt class="text-xs text-neutral-500 dark:text-neutral-400">Distanz</dt>
                            <dd class="mt-1 text-lg font-medium tabular-nums">{{ number_format($activity->distance_m / 1000, 1, ',', '.') }} km</dd>
                        </div>
                    @endif
                    @if ($activity->elevation_gain_m > 0)
                        <div>
                            <dt class="text-xs text-neutral-500 dark:text-neutral-400">Höhenmeter</dt>
                            <dd class="mt-1 text-lg font-medium tabular-nums">{{ number_format($activity->elevation_gain_m, 0, ',', '.') }} hm</dd>
                        </div>
                    @endif
                    @if ($activity->np_watts)
                        <div>
                            <dt class="text-xs text-neutral-500 dark:text-neutral-400">NP</dt>
                            <dd class="mt-1 text-lg font-medium tabular-nums">{{ $activity->np_watts }} W</dd>
                        </div>
                    @endif
                </dl>
            @elseif ($stravaConnected)
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Noch keine Aktivität importiert.</p>
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Verbinde Strava, um deine Trainings automatisch zu importieren.</p>
                <a
                    href="{{ route('strava.redirect') }}"
                    class="mt-4 inline-flex min-h-11 items-center rounded-xl bg-neutral-900 px-5 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                    Strava verbinden
                </a>
            @endif
        </section>

    </div>
</x-layout>
