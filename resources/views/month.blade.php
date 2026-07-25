<x-layout title="Monat — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="monat" />
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
        <h1 class="text-3xl font-semibold tracking-tight">{{ now()->isoFormat('MMMM') }}</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">Entwicklung der letzten 12 Monate</p>
    </header>

    <div class="mt-8 space-y-4">

        {{-- Gewicht --}}
        <section aria-labelledby="month-weight-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="month-weight-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Gewicht
            </h2>
            @if ($current['weight'])
                <p class="mt-3 text-4xl font-semibold tracking-tight tabular-nums">
                    {{ number_format($current['weight'], 1, ',', '.') }}<span class="ml-1 text-xl font-normal text-neutral-400">kg Ø</span>
                </p>
                @if ($previous && $previous['weight'])
                    @php $delta = $current['weight'] - $previous['weight']; @endphp
                    <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                        {{ $delta < 0 ? '−' : '+' }}{{ number_format(abs($delta), 1, ',', '.') }} kg zum Vormonat
                    </p>
                @endif
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Keine Messung in diesem Monat.</p>
            @endif

            @php $points = $months->pluck('weight')->filter()->values(); @endphp
            @if ($points->count() >= 2)
                @php
                    $min = $points->min();
                    $max = $points->max();
                    $range = max($max - $min, 0.1);
                    $coords = $points->map(fn ($w, $i) => round($i * (100 / ($points->count() - 1)), 1).','.round(24 - (($w - $min) / $range) * 20 + 2, 1))->implode(' ');
                @endphp
                <figure class="mt-5">
                    <svg viewBox="0 0 100 28" class="h-10 w-full text-neutral-300 dark:text-neutral-700" preserveAspectRatio="none" role="img" aria-label="Gewichtsverlauf der letzten 12 Monate">
                        <polyline points="{{ $coords }}" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                    </svg>
                    <figcaption class="mt-1 text-xs text-neutral-400 dark:text-neutral-500">
                        {{ number_format($min, 1, ',', '.') }}–{{ number_format($max, 1, ',', '.') }} kg · 12 Monate
                    </figcaption>
                </figure>
            @endif
        </section>

        {{-- Training --}}
        <section aria-labelledby="month-training-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="month-training-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Training
            </h2>
            <dl class="mt-4 space-y-2">
                @foreach ($months->slice(-6) as $month)
                    <div class="flex items-baseline justify-between gap-4 {{ $loop->last ? 'font-medium' : '' }}">
                        <dt class="text-sm {{ $loop->last ? '' : 'text-neutral-500 dark:text-neutral-400' }}">
                            {{ $month['month']->isoFormat('MMM YYYY') }}
                        </dt>
                        <dd class="text-sm tabular-nums">
                            {{ intdiv($month['trainingSeconds'], 3600) }}:{{ str_pad((string) intdiv($month['trainingSeconds'] % 3600, 60), 2, '0', STR_PAD_LEFT) }} h
                            · {{ $month['trainingSessions'] }} {{ $month['trainingSessions'] === 1 ? 'Einheit' : 'Einheiten' }}
                            @if ($month['strengthSessions'] > 0)
                                · {{ $month['strengthSessions'] }}× Kraft
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Gewohnheiten (dieser Monat) --}}
        <section aria-labelledby="month-habits-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="month-habits-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                Gewohnheiten diesen Monat
            </h2>
            @if ($loggedDays > 0)
                <dl class="mt-4 space-y-3">
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Erfasste Tage</dt>
                        <dd class="font-medium tabular-nums">{{ $loggedDays }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Feierabend eingehalten</dt>
                        <dd class="font-medium tabular-nums">{{ $feierabendDays }} {{ $feierabendDays === 1 ? 'Tag' : 'Tage' }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Mittag vorbereitet</dt>
                        <dd class="font-medium tabular-nums">{{ $mittagDays }} {{ $mittagDays === 1 ? 'Tag' : 'Tage' }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Naschfreie Tage</dt>
                        <dd class="font-medium tabular-nums">{{ $naschenFreiDays }}</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-sm text-neutral-500 dark:text-neutral-400">Schlaf Ø</dt>
                        <dd class="font-medium tabular-nums">{{ $schlafAvg !== null ? number_format($schlafAvg, 1, ',', '.') : '—' }}</dd>
                    </div>
                </dl>
            @else
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Diesen Monat noch nichts erfasst.</p>
            @endif
        </section>

    </div>
</x-layout>
