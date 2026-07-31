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
- **Wochenübersicht** (`/woche`): Aggregation in `WeekController` (Ø Gewicht, Trainingszeit, Habit-Zähler, Kraft-Einheiten, 8-Wochen-Sparkline als inline SVG). Woche = ISO (Mo–So).
- **Krafttraining** (`/kraft`): `exercises` (Workout A/B, SoftDeletes — Löschen erhält Historie), `strength_sessions` + `strength_entries` (ein Eintrag pro Übung: Gewicht × Wdh. × Sätze, kein Einzelsatz-Logging). Eintragsformular füllt letzte Werte vor (Progression). Übungen ohne Wdh./Sätze werden beim Speichern übersprungen.
- Kein `<fieldset>`/`<legend>` für Karten-Layouts — Browser rendern die Legend immer auf der Rahmenlinie; stattdessen `role="group"` + `aria-labelledby`.
- **Monatsübersicht** (`/monat`): 12-Monats-Aggregation in `MonthController` per `strftime('%Y-%m', …)` — SQLite-spezifisch, bei PostgreSQL-Umstieg auf `to_char()` umstellen.
- **Verlauf** (`/verlauf`): Langzeit-Charts als server-gerendertes SVG (`x-trend-chart`, keine Chart-Library). Zeiträume 6m/1j/alles; bei „alles" nur 7-Tage-Mittel ohne Einzelmesswerte. Unplausible Waagen-Messwerte (`HistoryController::PLAUSIBLE`) fliegen aus der *Auswertung*, bleiben aber in den Rohdaten.
- **FTP**: manuell gepflegte Historie (`ftp_entries`, Formular auf /verlauf). Dashboard zeigt FTP/kg (aktuelle FTP ÷ letztes Gewicht) und NP/kg aktivitätsbezogen (Gewicht zum Aktivitätszeitpunkt).
- **Rezepte** (`/rezepte`): Rubriken morgens/mittags/abends/snack, Sterne 1–5 für Geschmack/Aufwand/Kalorien (Kalorien-Sterne relativ zur Rubrik, 5 = sehr leicht; erneuter Tipp löscht). `RecipeSeeder` befüllt 40 Startgerichte, überschreibt aber nie eine bestehende Sammlung — auf dem Pi einmalig per `docker exec road-to-strong php artisan db:seed --class=RecipeSeeder --force`. Bewusst ohne LLM (Entscheidung 31.07.2026: Chatbot-Coach zurückgestellt, bis Habit-Daten existieren; Datenschutz-Grundsatzfrage).
- Sync: `strava:sync` / `withings:sync`, Scheduler alle 6 h (`routes/console.php`) — braucht lokal `php artisan schedule:work` oder einen Cron.

## Deployment (Heimserver / Pi)

- Läuft als Docker-Compose-Projekt unter `/mnt/piStorage/docker/road-to-strong/` auf dem Pi („heimserver"), erreichbar **nur im Tailnet**: `http://100.102.83.46:3008` (Port in der zentralen `PORTS.md` auf dem Pi registriert, bewusst an die Tailscale-IP gebunden).
- **`make deploy`** = rsync + `docker compose up -d --build` auf dem Pi (Muster wie dart-sheet). `make logs` zeigt Container-Logs.
- Zwei Container: `road-to-strong` (App via `php artisan serve`, für Single-User ausreichend) und `road-to-strong-scheduler` (`schedule:work` = Sync alle 6 h — kein Host-Cron nötig).
- **Server ist die Quelle** für `.env` und `data/database.sqlite` (Volume) — `deploy.sh` überschreibt beides nie; `.env.production` (lokal, gitignored) wird nur beim Erstdeploy kopiert. Der `APP_KEY` muss dem lokalen entsprechen, sonst sind die OAuth-Tokens unlesbar.
- OAuth-Callbacks bei Strava/Withings zeigen noch auf localhost — nur relevant, falls neu verbunden werden muss; laufende Token-Refreshes brauchen keine Callback-URL.
- Gefixt beim Deploy: `USER_HEIGHT_M` muss in **Metern** (1.78) angegeben werden, nicht cm — BMI-Werte wurden rückwirkend neu berechnet.

## Setup nach Clone

1. `composer install && npm install && npm run build`
2. `.env`: `STRAVA_CLIENT_ID/SECRET`, `WITHINGS_CLIENT_ID/SECRET`, `USER_HEIGHT_M`
3. `php artisan migrate && php artisan app:create-user <email>`
4. OAuth-Redirect-URIs bei den Providern: `{APP_URL}/auth/strava/callback` bzw. `{APP_URL}/auth/withings/callback`

- **PWA**: `public/manifest.webmanifest` + Icons (`public/icons/`, maskable mit 70%-Safe-Zone), Meta-Tags im Layout. Kein Service Worker (bewusst: kein Offline-Cache, keine Stale-Daten). Installation vom Handy setzt HTTPS-Erreichbarkeit voraus.

## Produktkompass (Details in docs/VISION.md)

Vier Ebenen: **Heute** (was tun?) → **Kennzahlen** (wo stehe ich?) → **Verlauf** (wie entwickle ich mich?)
→ **Insights** (warum?). Jedes Feature muss eine dieser Fragen besser beantworten, sonst gehört es
nicht ins Produkt. Tägliche Eingabe < 60 Sekunden. Diagramme sind erwünscht, wenn sie Trends zeigen
(Langzeitjournal) — nicht um Tagesschwankungen zu dramatisieren.

**W/kg-Regeln** (Details in VISION.md): FTP/kg ist die Hauptkennzahl; beste 20-min-/8-min-Leistung/kg
ergänzend; NP/kg nur aktivitätsbezogen anzeigen, nie als allgemeine Fitnesskennzahl.
Technisch: FTP kommt als manuell gepflegte Historie (Wert + gültig-ab-Datum) — nicht über den
Strava-Athletenscope; 20-/8-min-Bestwerte brauchen den Streams-Endpoint (per Aktivität, Rate-Limits)
und sind ein späterer Ausbau.

## Offene Punkte

- **Verlauf** (Frage 2): Langzeit-Charts über Monate/Jahre (Gewicht + 7-Tage-Mittel, Körperfett/Muskel, Trainingszeit/hm/kJ, Kraft, Habits) — nächster größerer Baustein
- **Insights** (Frage 3): Korrelationen aus eigenen Daten — Langfrist-Vision, braucht erst Monate an Habit-Daten
- Dashboard-Ideen aus der Vision: heutiger Trainingsplan, kurze Handlungsempfehlung, offene Gewohnheiten hervorheben
- Strava-Webhooks (vorbereitet durch Modul-Struktur, noch nicht gebaut)
- Kalorien: Strava liefert sie nur im Detail-Endpoint, Liste nicht — bei Bedarf Detail-Fetch ergänzen
- Deployment (angedachte Domain: strong.christoph-heim.de) — erst dann ist die PWA vom Handy installierbar
- Mögliche spätere Quellen: Apple Health, Garmin, Oura, WHOOP (Adapter-Muster wie Strava/Withings)
