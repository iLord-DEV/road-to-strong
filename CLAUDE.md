# Road to Strong

Persönliches Gesundheitscockpit (Single-User). Kein SaaS, kein Startup — Erfolg = Verhalten ändern, nicht Features.
Die vollständige Produktvision steht in `docs/VISION.md` — vor größeren Entscheidungen lesen.

## Stack

- Laravel 13, Blade, SQLite, Tailwind CSS 4 (Vite), keine SPA
- Tests: PHPUnit (`php artisan test`)
- Assets: `npm run build` (bzw. `npm run dev`)

## Architektur

- **Module** unter `app/Modules/{Strava,Withings}`: je ein `*Client` (OAuth + HTTP) und ein `*Importer` (Upsert in DB). Models bleiben in `app/Models`.
- **OAuth-Tokens** in `oauth_tokens` (ein Datensatz pro User+Provider), Access/Refresh-Token verschlüsselt (encrypted cast).
- **Rohdaten** jeder API-Antwort landen im `raw`-JSON-Feld (`activities.raw`, `body_measurements.raw`) — spätere Felder können daraus nachträglich befüllt werden.
- **Auth**: minimaler eigener Login (kein Breeze/Fortify), keine Registrierung. User anlegen: `php artisan app:create-user <email>`.

## Wichtige Entscheidungen

- `APP_TIMEZONE=Europe/Berlin`; Importer konvertieren API-Zeitstempel (UTC) beim Import in lokale Zeit. `config/app.php` liest `timezone` aus env (Laravel-13-Default war hartkodiert UTC).
- Strava-Import holt mit 7 Tagen Überlappung (späte Uploads), Withings mit 30 Tagen; Dedup über `strava_id` / `withings_grpid` (unique + updateOrCreate).
- Withings-Messwerte: `value * 10^unit`; BMI wird aus `USER_HEIGHT_M` berechnet (Withings liefert keinen).
- `VirtualRide` oder `trainer=true` ⇒ `indoor`. Sport-Labels: `Activity::sportLabel()` (VirtualRide = "Kickr", Rowing/VirtualRow = "WaterRower").
- OAuth-Callbacks werden in der Praxis doppelt aufgerufen (Reload/Redirect); der Einmal-State ist dann verbraucht → freundlicher Redirect aufs Dashboard statt 403.
- Systemschriften statt Webfonts (Datenschutz, Apple-Health-Look), keine externen Requests im Frontend.
- **Habits**: ein `daily_logs`-Datensatz pro Tag (unique user+date), alle Felder nullable. Erlaubte Werte zentral in `DailyLog::FIELDS`. Erfassung über reine POST-Formulare (`x-habit-option`, kein JS); erneutes Tippen auf den gewählten Wert löscht ihn wieder.
- **Wochenübersicht** (`/woche`): Aggregation in `WeekController` (Ø Gewicht, Trainingszeit, Habit-Zähler, 8-Wochen-Sparkline als inline SVG). Woche = ISO (Mo–So).
- Sync: `strava:sync` / `withings:sync`, Scheduler alle 6 h (`routes/console.php`) — braucht lokal `php artisan schedule:work` oder einen Cron.

## Setup nach Clone

1. `composer install && npm install && npm run build`
2. `.env`: `STRAVA_CLIENT_ID/SECRET`, `WITHINGS_CLIENT_ID/SECRET`, `USER_HEIGHT_M`
3. `php artisan migrate && php artisan app:create-user <email>`
4. OAuth-Redirect-URIs bei den Providern: `{APP_URL}/auth/strava/callback` bzw. `{APP_URL}/auth/withings/callback`

## Offene Punkte (Roadmap in docs/VISION.md)

- Strava-Webhooks (vorbereitet durch Modul-Struktur, noch nicht gebaut)
- Kalorien: Strava liefert sie nur im Detail-Endpoint, Liste nicht — bei Bedarf Detail-Fetch ergänzen
- PWA-Manifest/Icons (Design-Ziel, bewusst nach v0.1 verschoben)
- v0.3: Krafttraining (Workout A/B, letzte Werte anzeigen, Progression), v0.4: Analytics
