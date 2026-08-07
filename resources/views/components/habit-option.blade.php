@props(['field', 'value', 'label', 'current' => null, 'date' => null])

@php
    $selected = $current !== null
        && (string) (is_bool($current) ? (int) $current : $current) === (string) $value;
@endphp

{{-- Selected state is styled via aria-pressed so the JS enhancement only has to toggle the attribute --}}
<form method="POST" action="{{ route('habits.update') }}" data-habit>
    @csrf
    <input type="hidden" name="field" value="{{ $field }}">
    <input type="hidden" name="value" value="{{ $value }}">
    @if ($date)
        <input type="hidden" name="date" value="{{ $date->toDateString() }}">
    @endif
    <button
        type="submit"
        aria-pressed="{{ $selected ? 'true' : 'false' }}"
        class="min-h-11 min-w-11 rounded-full px-4 text-sm font-medium transition-colors focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:focus-visible:outline-neutral-100 bg-neutral-100 text-neutral-600 dark:bg-neutral-800 dark:text-neutral-300 aria-pressed:bg-neutral-900 aria-pressed:text-white dark:aria-pressed:bg-neutral-50 dark:aria-pressed:text-neutral-900"
    >
        {{ $label }}
    </button>
</form>
