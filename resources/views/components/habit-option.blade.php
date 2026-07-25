@props(['field', 'value', 'label', 'current' => null])

@php
    $selected = $current !== null
        && (string) (is_bool($current) ? (int) $current : $current) === (string) $value;
@endphp

<form method="POST" action="{{ route('habits.update') }}">
    @csrf
    <input type="hidden" name="field" value="{{ $field }}">
    <input type="hidden" name="value" value="{{ $value }}">
    <button
        type="submit"
        aria-pressed="{{ $selected ? 'true' : 'false' }}"
        class="min-h-11 min-w-11 rounded-full px-4 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 {{ $selected
            ? 'bg-neutral-900 text-white dark:bg-neutral-50 dark:text-neutral-900'
            : 'bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300' }}"
    >
        {{ $label }}
    </button>
</form>
