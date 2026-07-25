# Projekt: Road to Strong

## Vorwort

Dieses Projekt ist kein Startup.
Es soll nicht verkauft werden.
Es ist kein SaaS.
Es ist keine Fitnessplattform.

Es ist ein persönliches Werkzeug.

Der Erfolg dieses Projekts wird NICHT daran gemessen,
wie viele Features es besitzt,
sondern daran, ob ich in einem Jahr gesünder,
leichter und stärker bin.

Wenn eine neue Funktion mich vom Training abhält,
ist sie falsch.

Wenn eine Funktion mir hilft,
regelmäßig zu trainieren,
gesünder zu essen
oder bessere Gewohnheiten aufzubauen,
ist sie richtig.

Die App soll mich möglichst wenig beschäftigen.

Sie soll Entscheidungen vereinfachen,
nicht neue Arbeit erzeugen.

Automatisieren, was automatisiert werden kann.
Manuell erfassen, was nur ich wissen kann.

Das Produktziel lautet:

"Ein persönliches Gesundheitsdashboard,
das Training, Körperdaten und Gewohnheiten zusammenführt,
um langfristige Verhaltensänderungen zu unterstützen."

Nicht:

"Die beste Fitness-App."

---

## Hintergrund

Ich bin über 50.

Ich fahre Rennrad.

Ich trainiere mit

- Wahoo Kickr
- Outdoor-Rennrad
- WaterRower
- künftig regelmäßig Krafttraining.

Vor kurzem bin ich den Mont Ventoux gefahren.

Die Leistungsfähigkeit ist grundsätzlich vorhanden.

Mein Hauptproblem ist nicht fehlende Motivation,
sondern Alltagsgewohnheiten.

Typische Probleme:

- Homeoffice
- kein klarer Feierabend
- mittags spontane Essensentscheidungen (oft Döner)
- abends Chips oder Süßigkeiten als Belohnung
- Gewichtszunahme

Mein Ziel ist nicht kurzfristig Gewicht zu verlieren.

Mein Ziel ist, mein Leben dauerhaft umzubauen.

---

## Langfristige Ziele

- Gewicht etwa 70 kg
- regelmäßiges Krafttraining
- bessere Ernährung
- definierter Feierabend
- deutlich fitter am Rennrad
- langfristig wieder Alpen und große Pässe fahren

Die App soll diese Entwicklung begleiten.

---

## Produktphilosophie

Die App soll NICHT ständig Aufmerksamkeit verlangen.

Sie soll nicht gamifizieren.

Keine Badges.
Keine Levels.
Keine künstlichen Streaks.
Keine Push-Nachrichten.

Sie soll eher wie ein ruhiges Cockpit sein.

Jeden Morgen: "Wie stehe ich heute da?"
Jeden Abend: "Habe ich heute die richtigen Entscheidungen getroffen?"

Mehr nicht.

---

## Grundprinzip

Alles, was automatisch möglich ist, wird automatisch importiert.

Nur Verhalten wird manuell dokumentiert.

---

## Automatische Datenquellen

### Strava

OAuth Login. Import aller Aktivitäten. Speichern der Rohdaten.

Mindestens:

- Sportart
- Datum
- Dauer
- Distanz
- Höhenmeter
- Herzfrequenz
- Leistung
- NP
- Durchschnittsleistung
- Kalorien (wenn vorhanden)
- Kilojoule
- Relative Effort / Trainingsbelastung
- Indoor/Outdoor

Architektur so wählen, dass später problemlos weitere Daten übernommen werden können.

Webhooks vorbereiten.

### Withings

OAuth Login. Automatischer Import.

Mindestens:

- Gewicht
- Körperfett
- Muskelmasse
- Wasser
- BMI
- Zeitpunkt

Alle Rohdaten speichern.

---

## Was NICHT importiert werden kann

Diese Daten sollen möglichst einfach erfasst werden.

- Feierabend eingehalten? — Ja/Nein
- Mittag vorbereitet? — Ja/Nein
- Naschen — keines / bewusst / automatisch
- Craving — 0–3
- Schlafqualität — 1–5
- Energie — 1–5

---

## Krafttraining

Eigene Eingabe.

Trainingsplan: Workout A, Workout B.

Zu jeder Übung: Gewicht, Wiederholungen, Sätze.

Automatisch letztes Training anzeigen. Progression unterstützen.

Keine komplizierten Trainingspläne.

---

## Dashboard

Die wichtigste Seite. Nicht Charts. Nicht Statistiken. Heute.

Die App soll jeden Tag genau sagen: Was ist heute wichtig?

---

## Wochenübersicht

Gewichtstrend, Trainingszeit, Krafttraining, Gewohnheiten, Naschen, Schlaf. Nicht mehr.

## Monatsübersicht

Entwicklung. Keine täglichen Schwankungen. Lieber Trends.

---

## Design

Minimalistisch. Ruhig. Apple Health ähnlicher als Garmin.

Keine bunten Farben. Keine überladenen Dashboards.

Sehr viel Weißraum. Große Typografie.

Mobile First. PWA. Dark Mode.

---

## Technischer Stack

Laravel, Blade, SQLite zunächst (später PostgreSQL möglich), Tailwind CSS, Alpine.js falls nötig.

Keine React-SPA. Keine unnötige Komplexität. Server Side Rendering bevorzugen.

---

## Architektur

Saubere Domänen. Module: Dashboard, Strava, Withings, Strength, Habits, Analytics.

Repository Pattern nur wenn sinnvoll. Kein Overengineering. Keine Microservices. Kein Event Sourcing. YAGNI konsequent anwenden.

---

## Datenschutz

Alle Daten gehören ausschließlich mir.

Keine Cloud-Abhängigkeit außer Strava/Withings. Keine Werbung. Keine Tracker. Keine Analytics. Keine Cookies außer technisch notwendige. Keine Drittanbieter-Skripte.

---

## Entwicklungsprinzipien

Arbeite iterativ. Jeder Commit soll nutzbar sein.

Immer zuerst Funktion, danach Schönheit.

Tests dort, wo sie sinnvoll sind.

Keine Spekulation auf zukünftige Features.

---

## Roadmap

- **0.1** — Laravel, Login, Dashboard, Strava OAuth, Withings OAuth, täglicher Import
- **0.2** — Habits, Gewichtstrends, Wochenübersicht
- **0.3** — Krafttraining
- **0.4** — Analytics
- **1.0** — Persönliches Gesundheitscockpit

---

## Wichtigste Regel

Wenn während der Entwicklung ein neues Feature vorgeschlagen wird, hinterfrage es:

"Hilft dieses Feature dabei, das eigentliche Ziel zu erreichen?"

Wenn nein: einfachere Lösung vorschlagen.

Der Assistent ist nicht nur Programmierer, sondern Produktmanager — und beschützt dieses Projekt vor Feature Creep.

Das Ziel ist nicht, eine Fitness-App zu entwickeln. Das Ziel ist, Verhalten nachhaltig zu verändern.
