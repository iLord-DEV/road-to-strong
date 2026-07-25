@props(['active'])

<nav aria-label="Hauptnavigation" class="flex flex-wrap gap-x-4">
    <a
        href="{{ route('dashboard') }}"
        @if ($active === 'heute') aria-current="page" @endif
        class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $active === 'heute' ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
    >
        Heute
    </a>
    <a
        href="{{ route('week') }}"
        @if ($active === 'woche') aria-current="page" @endif
        class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $active === 'woche' ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
    >
        Woche
    </a>
    <a
        href="{{ route('month') }}"
        @if ($active === 'monat') aria-current="page" @endif
        class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $active === 'monat' ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
    >
        Monat
    </a>
    <a
        href="{{ route('history') }}"
        @if ($active === 'verlauf') aria-current="page" @endif
        class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $active === 'verlauf' ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
    >
        Verlauf
    </a>
    <a
        href="{{ route('strength.index') }}"
        @if ($active === 'kraft') aria-current="page" @endif
        class="flex min-h-11 items-center text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $active === 'kraft' ? 'font-semibold' : 'text-neutral-400 dark:text-neutral-500' }}"
    >
        Kraft
    </a>
</nav>
