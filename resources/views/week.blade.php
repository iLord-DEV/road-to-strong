<x-layout title="Woche — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="woche" />
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
        <h1 class="text-3xl font-semibold tracking-tight">Diese Woche</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">
            {{ $start->isoFormat('D. MMM') }} – {{ $end->isoFormat('D. MMM') }}
        </p>
    </header>

    <div class="mt-8 space-y-4">

        {{-- Gewichtstrend --}}
        <section aria-labelledby="week-weight-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="week-weight-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Gewicht
            </h2>
            @if ($weightThisWeek)
                <p class="mt-3 text-4xl font-semibold tracking-tight tabular-nums">
                    {{ number_format($weightThisWeek, 1, ',', '.') }}<span class="ml-1 text-xl font-normal text-neutral-400">kg Ø</span>
                </p>
                @if ($weightLastWeek)
                    @php $delta = $weightThisWeek - $weightLastWeek; @endphp
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $delta < 0 ? '−' : '+' }}{{ number_format(abs($delta), 1, ',', '.') }} kg zur Vorwoche
                    </p>
                @endif
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Keine Messung diese Woche.</p>
            @endif

            @php $points = $sparkline->filter()->values(); @endphp
            @if ($points->count() >= 2)
                @php
                    $min = $points->min();
                    $max = $points->max();
                    $range = max($max - $min, 0.1);
                    $coords = $points->map(fn ($w, $i) => round($i * (100 / ($points->count() - 1)), 1).','.round(24 - (($w - $min) / $range) * 20 + 2, 1))->implode(' ');
                @endphp
                <figure class="mt-5">
                    <svg viewBox="0 0 100 28" class="h-10 w-full text-neutral-300 dark:text-neutral-700" preserveAspectRatio="none" role="img" aria-label="Gewichtsverlauf der letzten 8 Wochen">
                        <polyline points="{{ $coords }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                    </svg>
                    <figcaption class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">Letzte 8 Wochen</figcaption>
                </figure>
            @endif
        </section>

        {{-- Training --}}
        <section aria-labelledby="week-training-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="week-training-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Training
            </h2>
            <p class="mt-3 text-4xl font-semibold tracking-tight tabular-nums">
                {{ intdiv($trainingThisWeek, 3600) }}:{{ str_pad((string) intdiv($trainingThisWeek % 3600, 60), 2, '0', STR_PAD_LEFT) }}<span class="ml-1 text-xl font-normal text-neutral-400">h</span>
            </p>
            <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                {{ $activityCount }} {{ $activityCount === 1 ? 'Einheit' : 'Einheiten' }}
                @php $trainingDelta = $trainingThisWeek - $trainingLastWeek; @endphp
                · {{ $trainingDelta < 0 ? '−' : '+' }}{{ intdiv(abs($trainingDelta), 3600) }}:{{ str_pad((string) intdiv(abs($trainingDelta) % 3600, 60), 2, '0', STR_PAD_LEFT) }} h zur Vorwoche
            </p>
        </section>

        {{-- Gewohnheiten --}}
        <section aria-labelledby="week-habits-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="week-habits-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Gewohnheiten
            </h2>
            @if ($loggedDays > 0)
                <dl class="mt-4 space-y-3">
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Feierabend eingehalten</dt>
                        <dd class="font-medium tabular-nums">{{ $feierabendDays }} {{ $feierabendDays === 1 ? 'Tag' : 'Tage' }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Mittag vorbereitet</dt>
                        <dd class="font-medium tabular-nums">{{ $mittagDays }} {{ $mittagDays === 1 ? 'Tag' : 'Tage' }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Naschen</dt>
                        <dd class="font-medium">
                            @if ($naschenCounts->isEmpty())
                                —
                            @else
                                {{ $naschenCounts->map(fn ($count, $kind) => ucfirst($kind).' '.$count)->implode(' · ') }}
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Schlaf Ø</dt>
                        <dd class="font-medium tabular-nums">{{ $schlafAvg !== null ? number_format($schlafAvg, 1, ',', '.') : '—' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Diese Woche noch nichts erfasst.</p>
            @endif
        </section>

    </div>
</x-layout>
