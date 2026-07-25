<x-layout title="Anmelden — Road to Strong">
    <div class="pt-10">
        <h1 class="text-3xl font-semibold tracking-tight">Road to Strong</h1>
        <p class="mt-2 text-neutral-500 dark:text-neutral-400">Dein persönliches Gesundheitscockpit.</p>

        <form method="POST" action="{{ route('login.attempt') }}" class="mt-12 space-y-6">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">E-Mail</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    required
                    autocomplete="email"
                    autofocus
                    value="{{ old('email') }}"
                    class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-600 dark:text-neutral-400">Passwort</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-2 block min-h-12 w-full rounded-xl border border-neutral-300 bg-white px-4 text-base focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:focus-visible:outline-neutral-100"
                >
            </div>

            <button
                type="submit"
                class="min-h-12 w-full rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
            >
                Anmelden
            </button>
        </form>
    </div>
</x-layout>
