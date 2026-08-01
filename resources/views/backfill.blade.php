<x-layout title="Nachtrag — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="heute" />
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Nachtrag</h1>
        <p class="mt-1 text-neutral-500 dark:text-neutral-400">
            @if ($day->isYesterday())
                Gestern, {{ $day->isoFormat('D. MMMM') }}
            @else
                {{ $day->isoFormat('dddd, D. MMMM') }}
            @endif
        </p>
    </header>

    <div class="mt-8 space-y-4">
        @include('partials.habits-card', ['log' => $log, 'day' => $day])

        <a
            href="{{ route('dashboard') }}"
            class="inline-flex min-h-11 items-center text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
        >
            ← Zurück zu Heute
        </a>
    </div>
</x-layout>
