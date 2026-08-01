@props(['row'])

<div class="grid grid-cols-[7.5rem_repeat(7,1.25rem)] items-center gap-x-1.5">
    <span class="pr-2 text-sm text-neutral-500 dark:text-neutral-400">{{ $row['label'] }}</span>
    @foreach ($row['dots'] as $dot)
        <span class="flex size-5 items-center justify-center" role="img" aria-label="{{ $row['label'] }}, {{ $dot['aria'] }}">
            @switch($dot['state'])
                @case('ja')
                    <span class="size-3.5 rounded-full bg-neutral-900 dark:bg-neutral-100"></span>
                    @break
                @case('nein')
                    <span class="size-3.5 rounded-full border-2 border-neutral-400 dark:border-neutral-600"></span>
                    @break
                @case('half')
                    <span class="flex size-3.5 items-center justify-center rounded-full border-2 border-neutral-900 dark:border-neutral-100">
                        <span class="size-1.5 rounded-full bg-neutral-900 dark:bg-neutral-100"></span>
                    </span>
                    @break
                @case('na')
                    <span class="text-xs text-neutral-300 dark:text-neutral-700">–</span>
                    @break
                @case('leer')
                    <span class="size-1.5 rounded-full bg-neutral-200 dark:bg-neutral-800"></span>
                    @break
                @default
                    <span class="size-3.5"></span>
            @endswitch
        </span>
    @endforeach
</div>
