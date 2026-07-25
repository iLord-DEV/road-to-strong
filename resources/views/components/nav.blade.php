@props(['active'])

<nav aria-label="Hauptnavigation" class="flex gap-5">
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
</nav>
