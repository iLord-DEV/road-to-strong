<x-layout title="Verlauf — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="verlauf" />
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

    <header class="mt-4 flex items-baseline justify-between">
        <h1 class="text-3xl font-semibold tracking-tight">Verlauf</h1>
        <nav aria-label="Zeitraum" class="flex gap-3">
            @foreach (['6m' => '6 M', '1j' => '1 J', 'alles' => 'Alles'] as $key => $label)
                <a
                    href="{{ route('history', $key === '1j' ? [] : ['zeitraum' => $key]) }}"
                    @if ($range === $key) aria-current="true" @endif
                    class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $range === $key ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
                >
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </header>

    <div class="mt-8 space-y-4">

        @if ($weightChart)
            <x-trend-chart
                title="Gewicht"
                :chart="$weightChart"
                :headline="number_format($weightChart['latest'], 1, ',', '.').'<span class=\'ml-1 text-xl font-normal text-neutral-400\'>kg</span>'"
                :subline="$range === 'alles' ? '7-Tage-Mittel' : '7-Tage-Mittel, helle Linie: Einzelmessungen'"
            />
        @else
            <section class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <h2 class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">Gewicht</h2>
                <p class="mt-3 text-neutral-500 dark:text-neutral-400">Noch nicht genug Messungen im gewählten Zeitraum.</p>
            </section>
        @endif

        @if ($fatChart)
            <x-trend-chart
                title="Körperfett"
                :chart="$fatChart"
                :headline="number_format($fatChart['latest'], 1, ',', '.').'<span class=\'ml-1 text-xl font-normal text-neutral-400\'>%</span>'"
            />
        @endif

        @if ($muscleChart)
            <x-trend-chart
                title="Muskelmasse"
                :chart="$muscleChart"
                :headline="number_format($muscleChart['latest'], 1, ',', '.').'<span class=\'ml-1 text-xl font-normal text-neutral-400\'>kg</span>'"
            />
        @endif

        @if ($training)
            <section aria-label="Training pro Monat" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
                <h2 class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                    Training pro Monat
                </h2>
                <figure class="mt-5">
                    <svg viewBox="0 0 100 40" class="h-32 w-full text-neutral-900 dark:text-neutral-100" preserveAspectRatio="none" role="img" aria-label="Trainingsstunden pro Monat, Maximum {{ $training['maxHours'] }} Stunden">
                        @foreach ($training['bars'] as $bar)
                            <rect x="{{ $bar['x'] }}" y="{{ $bar['y'] }}" width="{{ $bar['w'] }}" height="{{ $bar['h'] }}" fill="currentColor" opacity="0.85" />
                        @endforeach
                    </svg>
                    <figcaption class="mt-2 flex justify-between text-xs text-neutral-400 dark:text-neutral-500">
                        <span>{{ $training['from']->isoFormat('MMM YYYY') }}</span>
                        <span>max {{ number_format($training['maxHours'], 1, ',', '.') }} h/Monat</span>
                        <span>{{ now()->isoFormat('MMM YYYY') }}</span>
                    </figcaption>
                </figure>
                <dl class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">Stunden</dt>
                        <dd class="mt-1 font-medium tabular-nums">{{ number_format($training['totals']['hours'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">Kilometer</dt>
                        <dd class="mt-1 font-medium tabular-nums">{{ number_format($training['totals']['km'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">Höhenmeter</dt>
                        <dd class="mt-1 font-medium tabular-nums">{{ number_format($training['totals']['hm'], 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-neutral-500 dark:text-neutral-400">Kilojoule</dt>
                        <dd class="mt-1 font-medium tabular-nums">{{ number_format($training['totals']['kj'], 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </section>
        @endif

        @if ($sleepChart)
            <x-trend-chart
                title="Schlaf & Energie"
                :chart="$sleepChart"
                subline="Wochenmittel — dunkle Linie: Schlaf, helle Linie: Energie"
            />
        @endif

        @if ($habitChart)
            <x-trend-chart
                title="Gewohnheiten-Quote"
                :chart="$habitChart"
                subline="Anteil eingehaltener Tage pro Woche — dunkel: Feierabend, hell: Mittag vorbereitet"
            />
        @endif

        {{-- FTP-Historie --}}
        <section aria-labelledby="ftp-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
            <h2 id="ftp-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
                FTP-Historie
            </h2>

            @if ($ftpEntries->isEmpty())
                <p class="mt-3 text-sm text-neutral-500 dark:text-neutral-400">
                    Trag deine FTP nach jedem Test ein — daraus entsteht deine W/kg-Kennzahl auf dem Dashboard.
                </p>
            @else
                <ul class="mt-3 divide-y divide-neutral-100 dark:divide-neutral-800">
                    @foreach ($ftpEntries as $entry)
                        <li class="flex items-center justify-between gap-4 py-1">
                            <span class="tabular-nums">{{ $entry->watts }} W</span>
                            <span class="text-sm text-neutral-500 dark:text-neutral-400">{{ $entry->tested_at->isoFormat('D. MMM YYYY') }}</span>
                            <form method="POST" action="{{ route('ftp.destroy', $entry) }}">
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

            <form method="POST" action="{{ route('ftp.store') }}" class="mt-4 flex items-end gap-3">
                @csrf
                <div class="flex-1">
                    <label for="ftp-watts" class="block text-xs text-neutral-500 dark:text-neutral-400">FTP (Watt)</label>
                    <input
                        id="ftp-watts"
                        name="watts"
                        type="number"
                        inputmode="numeric"
                        required
                        min="50"
                        max="600"
                        class="mt-1 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-3 text-base tabular-nums focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                    >
                </div>
                <div class="flex-1">
                    <label for="ftp-date" class="block text-xs text-neutral-500 dark:text-neutral-400">Testdatum</label>
                    <input
                        id="ftp-date"
                        name="tested_at"
                        type="date"
                        required
                        value="{{ today()->toDateString() }}"
                        max="{{ today()->toDateString() }}"
                        class="mt-1 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-3 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                    >
                </div>
                <button
                    type="submit"
                    class="min-h-12 rounded-xl bg-neutral-900 px-5 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                    Speichern
                </button>
            </form>
            @error('watts')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </section>

    </div>
</x-layout>
