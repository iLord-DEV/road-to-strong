@props(['title', 'chart', 'headline' => null, 'subline' => null])

<section aria-label="{{ $title }}" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
    <h2 class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
        {{ $title }}
    </h2>

    @if ($headline)
        <p class="mt-3 text-4xl font-semibold tracking-tight tabular-nums">
            {!! $headline !!}
        </p>
    @endif
    @if ($subline)
        <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">{{ $subline }}</p>
    @endif

    <figure class="mt-5">
        <svg
            viewBox="0 0 100 40"
            class="h-40 w-full"
            preserveAspectRatio="none"
            role="img"
            aria-label="{{ $title }}: {{ number_format($chart['min'], 1, ',', '.') }} bis {{ number_format($chart['max'], 1, ',', '.') }} {{ $chart['unit'] }}"
        >
            @foreach ($chart['series'] as $series)
                <polyline
                    points="{{ $series['points'] }}"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="{{ $series['width'] }}"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    vector-effect="non-scaling-stroke"
                    class="{{ $series['class'] }}"
                />
            @endforeach
        </svg>
        <figcaption class="mt-2 flex justify-between text-xs text-neutral-400 dark:text-neutral-500">
            <span>{{ $chart['from']->isoFormat('MMM YYYY') }}</span>
            <span class="tabular-nums">{{ number_format($chart['min'], 1, ',', '.') }}–{{ number_format($chart['max'], 1, ',', '.') }} {{ $chart['unit'] }}</span>
            <span>{{ $chart['to']->isoFormat('MMM YYYY') }}</span>
        </figcaption>
    </figure>
</section>
