<x-layout title="{{ $recipe->name }} bearbeiten — Road to Strong">
    <div class="flex items-center justify-between">
        <x-nav active="rezepte" />
    </div>

    <header class="mt-4">
        <h1 class="text-3xl font-semibold tracking-tight">Rezept bearbeiten</h1>
    </header>

    <form method="POST" action="{{ route('recipes.update', $recipe) }}" class="mt-8 space-y-4 rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
        @csrf
        @method('PUT')
        @include('recipes.partials.fields', ['recipe' => $recipe])
        <div class="flex items-center gap-4">
            <button
                type="submit"
                class="min-h-12 flex-1 rounded-xl bg-neutral-900 font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:bg-neutral-50 dark:text-neutral-900 dark:focus-visible:outline-neutral-100"
            >
                Speichern
            </button>
            <a
                href="{{ route('recipes.show', $recipe) }}"
                class="flex min-h-12 items-center px-4 text-sm text-neutral-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-neutral-900 dark:text-neutral-500 dark:focus-visible:outline-neutral-100"
            >
                Abbrechen
            </a>
        </div>
    </form>
</x-layout>
