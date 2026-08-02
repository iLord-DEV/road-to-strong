{{-- Habit entry card for a given $day (Carbon) and its $log (DailyLog|null) --}}
<section aria-labelledby="habits-heading" class="rounded-2xl bg-white p-6 shadow-sm dark:bg-neutral-900">
    <h2 id="habits-heading" class="text-sm font-medium tracking-wide text-neutral-500 uppercase dark:text-neutral-400">
        Gewohnheiten
    </h2>

    <div class="mt-5 space-y-6">
        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Schlafqualität</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ([1, 2, 3, 4, 5] as $value)
                    <x-habit-option field="schlaf" :value="$value" :label="$value" :current="$log?->schlaf" :date="$day" />
                @endforeach
            </div>
            <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">1 = schlecht geschlafen · 5 = tief und erholt</p>
        </div>

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Durchgeschlafen</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-habit-option field="durchgeschlafen" value="1" label="Ja" :current="$log?->durchgeschlafen" :date="$day" />
                <x-habit-option field="durchgeschlafen" value="0" label="Nein" :current="$log?->durchgeschlafen" :date="$day" />
            </div>
        </div>

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Energie</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ([1, 2, 3, 4, 5] as $value)
                    <x-habit-option field="energie" :value="$value" :label="$value" :current="$log?->energie" :date="$day" />
                @endforeach
            </div>
            <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">1 = völlig platt · 5 = voller Tatendrang</p>
        </div>

        {{-- Nur an Arbeitstagen relevant — am Wochenende gibt es keinen Arbeitstag zu protokollieren --}}
        @unless ($day->isWeekend())
            <div>
                <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Arbeitsbeginn</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach ([6, 7, 8, 9, 10] as $value)
                        <x-habit-option field="arbeitsbeginn" :value="$value" :label="$value" :current="$log?->arbeitsbeginn" :date="$day" />
                    @endforeach
                </div>
                <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">Uhrzeit, zu der du angefangen hast</p>
            </div>

            <div>
                <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Mittag vorbereitet</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    <x-habit-option field="mittag_vorbereitet" value="1" label="Ja" :current="$log?->mittag_vorbereitet" :date="$day" />
                    <x-habit-option field="mittag_vorbereitet" value="0" label="Nein" :current="$log?->mittag_vorbereitet" :date="$day" />
                </div>
            </div>

            <div>
                <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Mittagspause gemacht</h3>
                <div class="mt-2 flex flex-wrap gap-2">
                    <x-habit-option field="mittagspause" value="1" label="Ja" :current="$log?->mittagspause" :date="$day" />
                    <x-habit-option field="mittagspause" value="0" label="Nein" :current="$log?->mittagspause" :date="$day" />
                </div>
            </div>
        @endunless

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Feierabend eingehalten</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-habit-option field="feierabend" value="1" label="Ja" :current="$log?->feierabend" :date="$day" />
                <x-habit-option field="feierabend" value="0" label="Nein" :current="$log?->feierabend" :date="$day" />
            </div>
        </div>

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Naschen</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-habit-option field="naschen" value="keines" label="Keines" :current="$log?->naschen" :date="$day" />
                <x-habit-option field="naschen" value="bewusst" label="Bewusst" :current="$log?->naschen" :date="$day" />
                <x-habit-option field="naschen" value="automatisch" label="Automatisch" :current="$log?->naschen" :date="$day" />
            </div>
        </div>

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Craving</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ([0, 1, 2, 3] as $value)
                    <x-habit-option field="craving" :value="$value" :label="$value" :current="$log?->craving" :date="$day" />
                @endforeach
            </div>
            <p class="mt-1.5 text-xs text-neutral-400 dark:text-neutral-500">0 = kein Verlangen · 3 = starkes Verlangen</p>
        </div>

        <div>
            <h3 class="text-sm text-neutral-500 dark:text-neutral-400">Cannabis am Vortag</h3>
            <div class="mt-2 flex flex-wrap gap-2">
                <x-habit-option field="cannabis_vortag" value="1" label="Ja" :current="$log?->cannabis_vortag" :date="$day" />
                <x-habit-option field="cannabis_vortag" value="0" label="Nein" :current="$log?->cannabis_vortag" :date="$day" />
            </div>
        </div>
    </div>
</section>
