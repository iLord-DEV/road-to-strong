<?php

namespace Database\Seeders;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    /**
     * Starter recipes: healthy, satisfying, built for steady weight loss.
     * Entry: [name, description, kcal, stars_aufwand, instructions].
     * kcal are per-portion estimates; stars_aufwand is pre-filled
     * (1 = kaum Aufwand … 5 = aufwendig), stars_kalorien is derived
     * per category (5 = sehr leicht), stars_geschmack stays personal.
     *
     * @var array<string, array<int, array{string, string, int, int, string}>>
     */
    private const RECIPES = [
        'morgens' => [
            ['Skyr mit Beeren & Walnüssen', '250 g Skyr, TK-Beeren, 5 Walnusshälften', 320, 1,
                "TK-Beeren am Vorabend in den Kühlschrank stellen oder kurz in der Mikrowelle auftauen.\nSkyr in eine Schüssel, Beeren samt Saft darüber, Walnüsse grob zerbrechen und darüber streuen."],
            ['Porridge mit Apfel & Zimt', '50 g Haferflocken, Wasser/Milch halb-halb, geriebener Apfel', 350, 2,
                "Haferflocken mit 125 ml Wasser und 125 ml Milch aufkochen, 3–4 Minuten köcheln und rühren.\nApfel grob reiben und unterheben, mit Zimt bestreuen. Wer mag: 1 TL Honig."],
            ['Rührei mit Tomaten auf Vollkornbrot', '2 Eier, Kirschtomaten, 1 Scheibe Vollkorn', 330, 2,
                "Kirschtomaten halbieren und in wenig Öl 2 Minuten anbraten.\nEier verquirlen, salzen, zu den Tomaten geben und stocken lassen. Auf das Brot geben, Pfeffer und Schnittlauch darüber."],
            ['Overnight Oats mit Chia & Himbeeren', 'Abends angesetzt — morgens nur aus dem Kühlschrank nehmen', 340, 1,
                "Abends: 40 g Haferflocken, 1 EL Chiasamen, 150 ml Milch und 100 g Joghurt verrühren, Himbeeren unterheben.\nAbgedeckt über Nacht in den Kühlschrank. Morgens umrühren, fertig."],
            ['Vollkornbrot mit Hüttenkäse & Gurke', 'Pfeffer und Schnittlauch drüber', 280, 1,
                "Brot mit einer dicken Schicht Hüttenkäse bestreichen.\nGurke in Scheiben darauf, kräftig pfeffern, Schnittlauch darüber."],
            ['Proteinshake mit Banane & Haferflocken', 'Proteinpulver, ½ Banane, 30 g Hafer, Wasser', 300, 1,
                "Alle Zutaten mit 300 ml Wasser (oder Milch für mehr Kalorien) 30 Sekunden mixen.\nIdeal, wenn es schnell gehen muss oder direkt nach dem Frühtraining."],
            ['Joghurt mit Leinsamen & Blaubeeren', 'Naturjoghurt 3,5 %, 1 EL geschrotete Leinsamen', 250, 1,
                "Joghurt mit Leinsamen verrühren und 5 Minuten quellen lassen — macht ihn cremiger und sättigender.\nBlaubeeren darüber."],
            ['Omelett mit Spinat & Feta', '2 Eier, Handvoll Spinat, 30 g Feta', 350, 2,
                "Spinat in der Pfanne zusammenfallen lassen.\nVerquirlte Eier darübergeben, Feta darüberbröseln, bei mittlerer Hitze stocken lassen und zusammenklappen."],
            ['Quark mit Birne & Mandeln', 'Magerquark mit etwas Milch cremig gerührt', 300, 1,
                "Magerquark mit einem Schuss Milch glatt rühren.\nBirne würfeln, unterheben, gehackte Mandeln darüber. Bei Bedarf etwas Zimt."],
            ['Vollkorn-Toast mit Avocado & Ei', '½ Avocado, 1 pochiertes oder gekochtes Ei', 380, 2,
                "Toast rösten, Avocado mit einer Gabel direkt darauf zerdrücken, salzen.\nEi 6–7 Minuten kochen (wachsweich), halbieren und darauflegen. Chiliflocken passen gut."],
        ],
        'mittags' => [
            ['Salatbowl mit Hähnchenbrust', 'Große Portion Blattsalat, gebratene Hähnchenstreifen, Joghurtdressing', 450, 2,
                "Hähnchenbrust in Streifen schneiden, würzen und 5–6 Minuten scharf anbraten.\nDressing: Joghurt, 1 TL Senf, Zitronensaft, Salz, Pfeffer. Über Salat, Gurke und Tomate geben, Hähnchen darauf."],
            ['Linsensuppe (Meal-Prep)', 'Großer Topf am Sonntag — 2 Portionen einfrieren', 400, 3,
                "Zwiebel, Karotte und Sellerie anschwitzen, 250 g rote Linsen und 1 l Gemüsebrühe dazu.\n15 Minuten köcheln, mit Kreuzkümmel, Paprika und einem Spritzer Essig abschmecken.\nErgibt 3–4 Portionen — den Rest portionsweise einfrieren."],
            ['Vollkornwrap mit Thunfisch & Gemüse', 'Thunfisch in Wasser, Paprika, Salat, Joghurt statt Mayo', 420, 1,
                "Thunfisch abtropfen, mit 2 EL Joghurt, Salz und Pfeffer vermengen.\nWrap mit Salat, Paprikastreifen und der Thunfischcreme belegen, fest einrollen, halbieren."],
            ['Quinoa-Bowl mit Kichererbsen & Ofengemüse', 'Ofengemüse vom Vortag verwenden', 480, 3,
                "Quinoa nach Packung kochen (oder vom Vortag).\nOfengemüse (Paprika, Zucchini, Süßkartoffel bei 200 °C, 25 Min) und abgespülte Kichererbsen darauf.\nDressing: Olivenöl, Zitrone, Kreuzkümmel."],
            ['Gemüseomelett mit Vollkornbrot', '2–3 Eier, Reste-Gemüse aus dem Kühlschrank', 400, 2,
                "Was der Kühlschrank hergibt (Paprika, Zucchini, Champignons) klein schneiden und anbraten.\nVerquirlte Eier darüber, stocken lassen. Mit einer Scheibe Vollkornbrot servieren."],
            ['Couscous-Salat mit Feta & Minze', 'Hält 2 Tage im Kühlschrank — perfekt vorbereitbar', 430, 2,
                "Couscous mit kochender Brühe übergießen (1:1), 5 Minuten quellen lassen, auflockern.\nGurke, Tomate, Feta und Minze unterheben, mit Zitrone und Olivenöl anmachen.\nDoppelte Menge machen — morgen ist das Mittagessen schon fertig."],
            ['Gefüllte Süßkartoffel mit Kräuterquark', 'Süßkartoffel in der Mikrowelle/Ofen, Quark mit Schnittlauch', 380, 2,
                "Süßkartoffel mehrfach einstechen, 8–10 Minuten in der Mikrowelle (oder 45 Min bei 200 °C im Ofen).\nLängs aufschneiden, Quark mit Schnittlauch, Salz und Pfeffer hineingeben."],
            ['Asia-Gemüsepfanne mit Tofu & Reis', 'TK-Wokgemüse spart Schnippelzeit', 450, 3,
                "Tofu würfeln, trocken tupfen und knusprig anbraten, herausnehmen.\nWokgemüse scharf anbraten, Tofu zurück, mit Sojasauce, Ingwer und Knoblauch ablöschen.\nMit 60 g Reis (Rohgewicht) servieren."],
            ['Kichererbsensalat mit Paprika & Zitrone', 'Dose Kichererbsen, Gurke, Paprika, Olivenöl, Zitrone — 5 Minuten', 400, 1,
                "Kichererbsen abspülen und abtropfen lassen.\nPaprika und Gurke würfeln, alles mit 1 EL Olivenöl, viel Zitronensaft, Salz und Kreuzkümmel vermengen.\nOptional Petersilie oder Feta darüber."],
            ['Vollkornpasta mit Tomaten-Linsen-Sugo', 'Rote Linsen kochen im Sugo mit — Protein satt', 480, 2,
                "Zwiebel und Knoblauch anschwitzen, passierte Tomaten und 50 g rote Linsen dazu.\n15 Minuten köcheln, mit Basilikum, Salz und einer Prise Zucker abschmecken.\nÜber 70 g Vollkornpasta (Rohgewicht) geben."],
        ],
        'abends' => [
            ['Ofenlachs mit Brokkoli', 'Lachsfilet und Brokkoli zusammen aufs Blech, Zitrone', 420, 2,
                "Ofen auf 200 °C. Brokkoliröschen mit wenig Öl aufs Blech, 10 Minuten vorgaren.\nLachs dazulegen, salzen, Zitronenscheiben darauf, weitere 12–15 Minuten backen."],
            ['Puten-Gemüse-Pfanne', 'Putenstreifen, Zucchini, Paprika, Sojasauce', 380, 2,
                "Putenstreifen scharf anbraten, herausnehmen.\nZucchini und Paprika in derselben Pfanne braten, Pute zurückgeben, mit Sojasauce und Pfeffer abschmecken."],
            ['Griechischer Salat mit Feta', 'Große Portion — Tomate, Gurke, Olive, 50 g Feta', 350, 1,
                "Tomaten und Gurke grob würfeln, rote Zwiebel in Ringen dazu.\nOliven und Feta darüber, mit Olivenöl, Oregano und wenig Salz anmachen. Dazu passt ein kleines Vollkornbrot."],
            ['Zucchini-Puffer mit Kräuterquark', 'Zucchini raspeln, ausdrücken, mit Ei und etwas Mehl braten', 330, 3,
                "Zucchini grob raspeln, kräftig salzen, 10 Minuten ziehen lassen und gut ausdrücken.\nMit 1 Ei und 2 EL Mehl mischen, portionsweise in wenig Öl goldbraun braten.\nDazu Quark mit Kräutern, Salz und Knoblauch."],
            ['Blumenkohlreis mit Garnelen', 'Blumenkohl im Mixer, Garnelen mit Knoblauch', 320, 2,
                "Blumenkohl im Mixer zu „Reis\" pulsen (oder fertig gekauft).\nGarnelen mit Knoblauch 2–3 Minuten braten, herausnehmen. Blumenkohlreis 5 Minuten in der Pfanne garen, Garnelen zurück, Zitrone darüber."],
            ['Tomatensuppe mit weißen Bohnen', 'Passierte Tomaten, Cannellini-Bohnen, Basilikum', 300, 2,
                "Zwiebel und Knoblauch anschwitzen, passierte Tomaten und einen Schuss Brühe dazu, 10 Minuten köcheln.\nAbgespülte Bohnen dazugeben und erwärmen. Mit Basilikum, Salz und Pfeffer abschmecken."],
            ['Gefüllte Paprika mit magerem Hack', 'Rinderhack 5 %, etwas Reis, im Ofen geschmort', 450, 3,
                "Hack mit Zwiebel anbraten, gekochten Reis und Tomatenmark untermischen, würzen.\nPaprika halbieren, entkernen, füllen und bei 180 °C ca. 30 Minuten backen.\nRest passierte Tomaten mit ins Blech — ergibt die Sauce."],
            ['Shakshuka', '2 Eier in würziger Tomaten-Paprika-Sauce', 350, 2,
                "Zwiebel und Paprika weich dünsten, Knoblauch, Kreuzkümmel und Paprikapulver dazu.\nPassierte Tomaten angießen, 10 Minuten einkochen. Zwei Mulden formen, Eier hineinschlagen, Deckel drauf und stocken lassen."],
            ['Halloumi mit Ofengemüse', 'Halloumi in der Pfanne, Gemüse nach Saison', 400, 2,
                "Gemüse (Zucchini, Paprika, Champignons) mit wenig Öl bei 200 °C ca. 25 Minuten rösten.\nHalloumi in Scheiben ohne Fett goldbraun braten und darauflegen. Zitrone darüber."],
            ['Kürbissuppe mit Ingwer', 'Hokkaido, Ingwer, etwas Kokosmilch', 280, 2,
                "Hokkaido würfeln (Schale bleibt dran), mit Zwiebel und Ingwer anschwitzen.\nMit Brühe bedecken, 15 Minuten weich kochen, pürieren. Ein Schuss Kokosmilch, Salz, Muskat."],
        ],
        'snack' => [
            ['Apfel mit Erdnussbutter', '1 Apfel, 1 EL Erdnussbutter (ohne Zucker)', 180, 1,
                "Apfel in Spalten schneiden, in die Erdnussbutter dippen.\n1 EL abmessen — nicht aus dem Glas löffeln."],
            ['Handvoll Mandeln', '25 g — abzählen, nicht aus der Tüte', 150, 1,
                "25 g sind etwa 20 Mandeln. In eine kleine Schale, Tüte zurück in den Schrank.\nAus der Tüte essen endet nie bei 25 g."],
            ['Gemüsesticks mit Hummus', 'Karotte, Gurke, Paprika, 2 EL Hummus', 150, 1,
                "Gemüse in Sticks schneiden — geht auch am Sonntag auf Vorrat für 2–3 Tage (in Wasser im Kühlschrank).\n2 EL Hummus in eine Schale, nicht der ganze Becher auf den Tisch."],
            ['Skyr pur mit Zimt', 'Sättigt enorm bei wenig Kalorien', 100, 1,
                "150 g Skyr in eine Schale, großzügig Zimt darüber.\nKlingt langweilig, hält aber locker bis zur nächsten Mahlzeit."],
            ['2 harte Eier', 'Am Sonntag einen Vorrat kochen', 140, 1,
                "Eier 9–10 Minuten kochen, abschrecken.\nGeschält halten sie im Kühlschrank 3–4 Tage — der schnellste Protein-Snack, den es gibt."],
            ['Edamame', 'TK, kurz in Salzwasser — Protein-Snack', 120, 1,
                "TK-Edamame 5 Minuten in Salzwasser kochen, abgießen.\nMit etwas grobem Salz aus der Schote essen — dauert lange, sättigt gut."],
            ['Magerquark mit Kakao', '1 TL echter Backkakao, etwas Süßstoff', 130, 1,
                "150 g Magerquark mit 1 TL Backkakao (ungesüßt) und Süßstoff oder etwas Honig verrühren.\nSchmeckt wie Schokocreme, ist aber ein Protein-Snack — der Abend-Retter."],
            ['Banane', 'Ideal vor dem Training statt danach zu snacken', 100, 1,
                "Keine Zubereitung — das ist der Punkt.\nVor der Kickr-Einheit essen, dann ist der Süßhunger danach kleiner."],
            ['Dunkle Schokolade (85 %)', '2 Stücke, bewusst genossen — kein Verbot, ein Ritual', 110, 1,
                "2 Stücke abbrechen, Tafel zurück in den Schrank, hinsetzen, langsam essen.\nDas ist „Naschen: bewusst\" — genau so ist es gedacht."],
            ['Proteinriegel', 'Für unterwegs — auf < 200 kcal achten', 200, 1,
                "Für die Schublade oder die Trikottasche.\nBeim Kauf auf unter 200 kcal und über 15 g Protein achten — viele „Fitness\"-Riegel sind Süßigkeiten."],
        ],
    ];

    public function run(): void
    {
        foreach (User::all() as $user) {
            // Never overwrite an existing personal collection
            if (Recipe::where('user_id', $user->id)->exists()) {
                continue;
            }

            foreach (self::RECIPES as $category => $recipes) {
                foreach ($recipes as [$name, $description, $kcal, $aufwand, $instructions]) {
                    Recipe::create([
                        'user_id' => $user->id,
                        'category' => $category,
                        'name' => $name,
                        'description' => $description,
                        'instructions' => $instructions,
                        'kcal' => $kcal,
                        'stars_aufwand' => $aufwand,
                        'stars_kalorien' => $this->kalorienStars($category, $kcal),
                    ]);
                }
            }
        }
    }

    /**
     * 5 = sehr leicht, 1 = üppig — relative to what's normal per category.
     */
    private function kalorienStars(string $category, int $kcal): int
    {
        $thresholds = match ($category) {
            'morgens' => [250, 300, 350, 400],
            'mittags' => [380, 420, 460, 500],
            'abends' => [300, 350, 400, 450],
            'snack' => [100, 130, 160, 200],
        };

        foreach ($thresholds as $index => $limit) {
            if ($kcal <= $limit) {
                return 5 - $index;
            }
        }

        return 1;
    }
}
