@props(['recipe', 'dimension', 'label', 'current' => null])

<div class="flex items-center gap-2">
    <span class="w-20 shrink-0 text-xs text-neutral-500 dark:text-neutral-400">{{ $label }}</span>
    <div class="flex" role="group" aria-label="{{ $label }} bewerten">
        @foreach ([1, 2, 3, 4, 5] as $stars)
            <form method="POST" action="{{ route('recipes.rate', $recipe) }}">
                @csrf
                <input type="hidden" name="dimension" value="{{ $dimension }}">
                <input type="hidden" name="stars" value="{{ $stars }}">
                <button
                    type="submit"
                    aria-label="{{ $label }}: {{ $stars }} von 5"
                    aria-pressed="{{ $current === $stars ? 'true' : 'false' }}"
                    class="flex min-h-11 min-w-9 items-center justify-center text-lg focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $current !== null && $stars <= $current
                        ? 'text-neutral-900 dark:text-neutral-100'
                        : 'text-neutral-300 dark:text-neutral-700' }}"
                >{{ $current !== null && $stars <= $current ? '★' : '☆' }}</button>
            </form>
        @endforeach
    </div>
</div>
