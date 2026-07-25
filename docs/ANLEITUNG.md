# Road to Strong — Anwendungsanleitung

Dein persönliches Gesundheitscockpit: Training und Körperdaten kommen automatisch, nur dein Verhalten trägst du selbst ein. Zwei Blicke am Tag reichen.

---

## App starten

```bash
php artisan serve            # App auf http://localhost:8000
php artisan schedule:work    # automatischer Sync (in zweitem Terminal)
```

Anmelden mit E-Mail und Passwort. Passwort ändern:

```bash
php artisan app:create-user info@webdesign-heim.de
```

---

## Der Tagesrhythmus

### Morgens (30 Sekunden)

1. **Auf die Waage stellen.** Mehr nicht — Withings synchronisiert automatisch, das Dashboard zeigt Gewicht und Trend.
2. Auf **Heute** kurz **Schlafqualität** (1–5) und **Energie** (1–5) antippen.

### Mittags

- **Mittag vorbereitet?** → Ja/Nein antippen. Der Sinn: die spontane Döner-Entscheidung sichtbar machen.

### Abends (30 Sekunden)

- **Feierabend eingehalten?** → Ja/Nein
- **Naschen** → Keines / Bewusst / Automatisch
  („Bewusst" = geplant und genossen. „Automatisch" = vor dem Bildschirm passiert.)
- **Craving** (0–3) → wie stark war der Drang, unabhängig davon, ob du nachgegeben hast.

**Tipp:** Ein zweiter Tipp auf den gewählten Wert löscht ihn wieder (falls vertippt).

---

## Training

### Rad, Rudern & Co. (automatisch)

Alle Strava-Aktivitäten werden automatisch importiert — alle 6 Stunden, solange `schedule:work` (oder ein Cron) läuft. Sofort importieren:

```bash
php artisan strava:sync
php artisan withings:sync
```

### Krafttraining (manuell)

1. **Einmalig:** Unter **Kraft → Übungen bearbeiten** deine Übungen für Workout A und Workout B anlegen.
2. **Nach jedem Training:** **Kraft → Workout A/B eintragen**. Die Felder sind mit den letzten Werten vorausgefüllt — Progression heißt: eine Zahl leicht erhöhen, speichern, fertig.
3. Übungen, die du ausgelassen hast, einfach leer lassen — sie werden übersprungen.

Das Datum ist wählbar, falls du das Eintragen mal vergisst. Gelöschte Übungen nehmen ihre Historie nicht mit ins Grab — alte Trainings bleiben erhalten.

---

## Woche und Monat lesen

- **Woche** — die ehrliche Bilanz: Ø Gewicht zur Vorwoche, Trainingszeit, Kraft-Einheiten, Feierabend-/Mittag-Tage, Naschen, Schlaf. Gut für den Sonntagabend.
- **Monat** — die Richtung: Gewichtsverlauf über 12 Monate, Trainingsvolumen der letzten 6 Monate. Tagesschwankungen sind hier bewusst weggemittelt — zählt nur der Trend.

---

## Verbindungen (Strava / Withings)

Einmal verbunden, erneuern sich die Zugänge automatisch. Falls doch mal ein Import ausbleibt:

1. `php artisan strava:sync` bzw. `withings:sync` im Terminal ausführen und Fehlermeldung lesen.
2. Zugriff bei [Strava](https://www.strava.com/settings/apps) bzw. [Withings](https://account.withings.com) widerrufen und in der App neu verbinden (Buttons erscheinen auf dem Dashboard, sobald keine Verbindung besteht).

Die Zugangsdaten der API-Apps stehen in der `.env` (`STRAVA_CLIENT_ID` usw.).

---

## Befehle im Überblick

| Befehl | Zweck |
|---|---|
| `php artisan serve` | App starten |
| `php artisan schedule:work` | automatischer Sync alle 6 h |
| `php artisan strava:sync` | Aktivitäten sofort importieren |
| `php artisan withings:sync` | Messungen sofort importieren |
| `php artisan app:create-user <email>` | Benutzer anlegen / Passwort ändern |
| `php artisan test` | Tests ausführen |

---

## Wenn etwas klemmt

- **Keine neuen Aktivitäten?** Läuft `schedule:work`? Sonst manuell syncen (siehe oben).
- **Gewicht fehlt?** Erst wiegen, dann syncen — Withings überträgt die Messung erst, wenn die Waage online war.
- **Passwort vergessen?** `php artisan app:create-user <deine-mail>` setzt ein neues.
- **Seite kaputt nach Update?** `npm run build` und `php artisan migrate` ausführen.

---

## Die Philosophie (als Erinnerung)

Die App will dich nicht beschäftigen. Kein Streak, keine Badges, keine Push-Nachrichten. Sie stellt dir nur zweimal am Tag dieselbe Frage: **„Habe ich heute die richtigen Entscheidungen getroffen?"** — und zeigt dir einmal im Monat, dass sich die Antworten summieren.
