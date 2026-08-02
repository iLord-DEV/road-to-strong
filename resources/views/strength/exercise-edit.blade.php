<x-layout title="Übung bearbeiten — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="kraft" />
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Übung bearbeiten</h1>
    </header>

    <form method="POST" action="{{ route('exercises.update', $exercise) }}" class="mt-8 space-y-4 rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Name</label>
            <input
                id="name"
                name="name"
                type="text"
                required
                maxlength="100"
                value="{{ old('name', $exercise->name) }}"
                class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
            >
            @error('name')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <fieldset>
            <legend class="text-sm font-medium text-neutral-600 dark:text-neutral-400">Workout</legend>
            <div class="mt-2 flex gap-4">
                @foreach (\App\Models\Exercise::WORKOUTS as $workout)
                    <label class="flex min-h-11 items-center gap-2">
                        <input
                            type="radio"
                            name="workout"
                            value="{{ $workout }}"
                            @checked(old('workout', $exercise->workout) === $workout)
                            class="size-4 accent-neutral-900 dark:accent-neutral-100"
                        >
                        <span>{{ $workout }}</span>
                    </label>
                @endforeach
            </div>
        </fieldset>

        <div>
            <label for="video_url" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Technik-Video (URL, optional)</label>
            <input
                id="video_url"
                name="video_url"
                type="url"
                maxlength="255"
                value="{{ old('video_url', $exercise->video_url) }}"
                placeholder="https://www.youtube.com/…"
                class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
            >
            @error('video_url')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="min-h-12 flex-1 rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
            >
                Speichern
            </button>
            <a
                href="{{ route('exercises.index') }}"
                class="flex min-h-12 items-center px-4 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
            >
                Abbrechen
            </a>
        </div>
    </form>
</x-layout>
