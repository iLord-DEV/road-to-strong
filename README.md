# Road to Strong

Persönliches Gesundheitscockpit: führt Strava-Training, Withings-Körperdaten, Gewohnheiten und Krafttraining in einem ruhigen Dashboard zusammen. Kein SaaS, keine Gamification — ein Werkzeug für dauerhafte Verhaltensänderung.

Road to Strong ist kein Fitness-Tracker. Es ist ein persönliches Betriebssystem für gesunde Entscheidungen.

- **[Anwendungsanleitung](docs/ANLEITUNG.md)** — täglicher Gebrauch, Befehle, Fehlerbehebung
- **[Produktvision](docs/VISION.md)** — warum es diese App gibt und was sie bewusst nicht tut
- **[CLAUDE.md](CLAUDE.md)** — technische Architektur und Entscheidungen

## Setup

```bash
composer install && npm install && npm run build
cp .env.example .env && php artisan key:generate
# .env: STRAVA_CLIENT_ID/SECRET, WITHINGS_CLIENT_ID/SECRET, USER_HEIGHT_M
php artisan migrate
php artisan app:create-user <email>
php artisan serve
```

Stack: Laravel, Blade, SQLite, Tailwind CSS. Tests: `php artisan test`.
