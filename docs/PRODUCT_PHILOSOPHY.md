# Road to Strong
## Product Philosophy & Vision

> **Road to Strong dokumentiert nicht mein Training. Road to Strong dokumentiert mein Leben zwischen den Trainingseinheiten.**

---

# Mission

Road to Strong ist kein Startup.

Es soll nicht verkauft werden.

Es ist kein SaaS.

Es ist keine Fitnessplattform.

Es ist ein persönliches Werkzeug.

Der Erfolg dieses Projekts wird nicht daran gemessen,

- wie viele Features existieren,
- wie modern der Tech-Stack ist,
- oder wie schön der Code aussieht.

Der Erfolg wird daran gemessen,

ob ich in einem Jahr

- gesünder,
- leichter,
- stärker,
- leistungsfähiger,
- und zufriedener mit meinem Alltag bin.

Wenn eine neue Funktion mich vom Training abhält,
ist sie falsch.

Wenn eine Funktion mir hilft,

- bessere Entscheidungen zu treffen,
- regelmäßiger Sport zu treiben,
- besser zu essen,
- meinen Feierabend einzuhalten,
- oder gesündere Gewohnheiten aufzubauen,

ist sie richtig.

Die App soll mich möglichst wenig beschäftigen.

Sie soll mein Leben vereinfachen.

Nicht komplizierter machen.

---

# Das Problem

Es gibt bereits hervorragende Apps.

Strava dokumentiert mein Training.

Withings dokumentiert meinen Körper.

Apple Health sammelt Gesundheitsdaten.

Garmin Connect analysiert Trainings.

Keine dieser Apps beantwortet jedoch die eigentliche Frage:

**Warum entwickeln sich mein Körper und meine Leistungsfähigkeit so, wie sie es tun?**

Road to Strong soll genau diese Lücke schließen.

Die App verbindet automatisch erfasste Daten mit den Informationen,
die nur ich selbst kennen kann.

Nicht das Training ist mein Problem.

Die Entscheidungen davor und danach sind das eigentliche Produkt.

---

# Die drei Fragen

Road to Strong beantwortet jeden Tag nur drei Fragen.

## 1. Heute

> Was sollte ich heute tun?

Die App unterstützt meine Entscheidungen für den heutigen Tag.

Sie zeigt beispielsweise:

- aktuelles Gewicht
- Schlaf
- Energie
- heutiger Trainingsplan
- letzte Strava-Aktivität
- offene Gewohnheiten
- kurze Handlungsempfehlung

Die Startseite soll ruhig wirken.

Nicht überladen.

Keine Informationsflut.

---

## 2. Verlauf

> Wie habe ich mich entwickelt?

Road to Strong ist ausdrücklich auch ein Langzeitjournal.

Ich möchte Entwicklungen über Wochen,
Monate
und Jahre verfolgen können.

Zum Beispiel:

- Gewicht
- 7-Tage-Mittel
- Körperfett
- Muskelmasse
- Trainingszeit
- Höhenmeter
- Kilojoule
- Kraftentwicklung
- Gewohnheiten
- Schlaf
- Energie

Visualisierungen sind ausdrücklich Teil des Produkts.

Nicht um zu beeindrucken.

Sondern um Entwicklungen verständlich zu machen.

Diagramme sollen Trends zeigen.

Nicht tägliche Schwankungen dramatisieren.

---

## 3. Insights

> Warum entwickle ich mich so?

Das ist die langfristige Vision.

Road to Strong soll Zusammenhänge erkennen.

Nicht durch generische KI.

Sondern durch meine eigenen Daten.

Beispiele:

- Snacke ich häufiger nach langen Arbeitstagen?
- Hilft vorbereitetes Mittagessen?
- Welche Auswirkungen hat Schlaf auf mein Training?
- Wie verändert sich meine Leistung pro Kilogramm?
- Welche Gewohnheiten gehen guten Trainingswochen voraus?

Die App soll mich besser verstehen.

Nicht belehren.

---

# Datenquellen

## Automatisch

Alles,
was automatisch importiert werden kann,
soll automatisch importiert werden.

Aktuelle Quellen:

- Strava
- Withings

Spätere mögliche Quellen:

- Apple Health
- Garmin
- Oura
- WHOOP

Alle Rohdaten werden dauerhaft gespeichert.

Nicht nur berechnete Kennzahlen.

Auswertungen werden jederzeit aus den Rohdaten berechnet.

Dadurch können auch Jahre später neue Analysen über alte Daten erstellt werden.

---

## Manuell

Nur Informationen,
die keine API kennen kann.

Zum Beispiel:

- Feierabend eingehalten
- Mittag vorbereitet
- Naschen
- Craving
- subjektive Energie
- subjektiver Schlaf
- Krafttraining

Die tägliche Eingabe soll weniger als 60 Sekunden dauern.

Wenn ein neues Feature zusätzliche Eingaben verlangt,

muss hinterfragt werden,

ob der Nutzen den Aufwand rechtfertigt.

---

# Produktprinzipien

## Automatisieren

Automatisieren,
was automatisiert werden kann.

## Vereinfachen

So wenig Eingaben wie möglich.

## Langfristig denken

Die App soll viele Jahre nutzbar bleiben.

## Trends statt Momentaufnahmen

Entscheidungen sollen auf Entwicklungen beruhen,
nicht auf einzelnen Tagen.

## Verhalten vor Zahlen

Das Ziel ist nicht,
möglichst viele Kennzahlen zu sammeln.

Das Ziel ist,
mein Verhalten nachhaltig zu verändern.

---

# Designprinzipien

Die App soll ruhig wirken.

Nicht verspielt.

Nicht gamifiziert.

Keine Badges.

Keine Levels.

Keine künstlichen Streaks.

Keine künstliche Motivation.

Apple Health ist näher am gewünschten Stil
als Garmin Connect.

Viel Weißraum.

Große Typografie.

Klare Hierarchie.

Mobile First.

Dark Mode.

PWA.

---

# Datenschutz

Alle Daten gehören ausschließlich mir.

Keine Werbung.

Keine Tracker.

Keine Analytics.

Keine unnötigen Cookies.

Keine Cloud-Abhängigkeit außer den bewusst verbundenen APIs.

---

# Architektur

Training,
Körperdaten
und Verhalten
sind getrennte Domänen.

Externe Systeme werden über Adapter angebunden.

Rohdaten bleiben unverändert erhalten.

Auswertungen entstehen daraus.

Die Architektur soll Erweiterungen ermöglichen,

aber kein Overengineering betreiben.

YAGNI gilt konsequent.

---

# Was Road to Strong NICHT werden soll

Road to Strong soll niemals

- Strava ersetzen
- Withings ersetzen
- Apple Health ersetzen
- Garmin Connect ersetzen
- MyFitnessPal ersetzen

Road to Strong ist die Verbindung zwischen diesen Systemen.

Es beantwortet Fragen,
die keines dieser Systeme allein beantworten kann.

---

# Entwicklungsregeln

Jedes neue Feature muss mindestens eine dieser Fragen besser beantworten:

1. Was sollte ich heute tun?
2. Wie habe ich mich entwickelt?
3. Warum entwickle ich mich so?

Wenn keine dieser Fragen besser beantwortet wird,

gehört das Feature nicht in dieses Produkt.

---

# Rolle von Claude Code

Du bist nicht nur Entwickler.

Du bist Produktmanager,
Softwarearchitekt
und Sparringspartner.

Schütze dieses Projekt aktiv vor Feature Creep.

Hinterfrage neue Ideen.

Schlage einfachere Lösungen vor,
wenn sie denselben Nutzen bringen.

Erinnere mich an diese Produktvision,
wenn ich beginne,
mich in technischen Details zu verlieren.

Hilf mir dabei,

Road to Strong klein,
klar
und fokussiert zu halten.

Die Einfachheit ist kein Kompromiss.

Sie ist Teil des Produkts.
