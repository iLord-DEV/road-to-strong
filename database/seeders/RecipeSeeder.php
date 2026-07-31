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
     * Descriptions carry full quantities so the kcal estimate is honest.
     * stars_aufwand is pre-filled (1 = kaum Aufwand … 5 = aufwendig),
     * stars_kalorien is derived per category (5 = sehr leicht),
     * stars_geschmack stays personal.
     *
     * @var array<string, array<int, array{string, string, int, int, string}>>
     */
    private const RECIPES = [
        'morgens' => [
            ['Skyr mit Beeren & Walnüssen', '250 g Skyr, 125 g TK-Beeren, 5 Walnusshälften (10 g)', 320, 1,
                "TK-Beeren am Vorabend in den Kühlschrank stellen oder kurz in der Mikrowelle auftauen.\n250 g Skyr in eine Schüssel, Beeren samt Saft darüber, Walnüsse grob zerbrechen und darüber streuen."],
            ['Porridge mit Apfel & Zimt', '50 g Haferflocken, 125 ml Milch (1,5 %) + 125 ml Wasser, 1 Apfel, Zimt', 350, 2,
                "50 g Haferflocken mit 125 ml Wasser und 125 ml Milch aufkochen, 3–4 Minuten köcheln und rühren.\nApfel grob reiben und unterheben, mit Zimt bestreuen. Wer mag: 1 TL Honig (+20 kcal)."],
            ['Rührei mit Tomaten auf Vollkornbrot', '2 Eier, 100 g Kirschtomaten, 1 Scheibe Vollkornbrot (50 g), 1 TL Öl', 330, 2,
                "100 g Kirschtomaten halbieren und in 1 TL Öl 2 Minuten anbraten.\n2 Eier verquirlen, salzen, zu den Tomaten geben und stocken lassen. Auf das Brot geben, Pfeffer und Schnittlauch darüber."],
            ['Overnight Oats mit Chia & Himbeeren', '40 g Haferflocken, 1 EL Chiasamen (10 g), 150 ml Milch, 100 g Joghurt, 100 g Himbeeren', 340, 1,
                "Abends: 40 g Haferflocken, 1 EL Chiasamen, 150 ml Milch und 100 g Joghurt verrühren, 100 g Himbeeren unterheben.\nAbgedeckt über Nacht in den Kühlschrank. Morgens umrühren, fertig."],
            ['Vollkornbrot mit Hüttenkäse & Gurke', '1 Scheibe Vollkornbrot (50 g), 150 g Hüttenkäse, ⅓ Gurke', 280, 1,
                "Brot mit 150 g Hüttenkäse bestreichen.\n⅓ Gurke in Scheiben darauf, kräftig pfeffern, Schnittlauch darüber."],
            ['Proteinshake mit Banane & Haferflocken', '30 g Proteinpulver, ½ Banane, 30 g Haferflocken, 300 ml Wasser', 300, 1,
                "Alle Zutaten mit 300 ml Wasser 30 Sekunden mixen (mit Milch statt Wasser: +70 kcal).\nIdeal, wenn es schnell gehen muss oder direkt nach dem Frühtraining."],
            ['Joghurt mit Leinsamen & Blaubeeren', '250 g Naturjoghurt 3,5 %, 1 EL geschrotete Leinsamen (10 g), 100 g Blaubeeren', 250, 1,
                "250 g Joghurt mit 1 EL Leinsamen verrühren und 5 Minuten quellen lassen — macht ihn cremiger und sättigender.\n100 g Blaubeeren darüber."],
            ['Omelett mit Spinat & Feta', '2 Eier, 100 g Blattspinat, 30 g Feta, 1 TL Öl', 350, 2,
                "100 g Spinat in 1 TL Öl zusammenfallen lassen.\n2 verquirlte Eier darübergeben, 30 g Feta darüberbröseln, bei mittlerer Hitze stocken lassen und zusammenklappen."],
            ['Quark mit Birne & Mandeln', '250 g Magerquark, Schuss Milch, 1 Birne, 10 g gehackte Mandeln', 300, 1,
                "250 g Magerquark mit einem Schuss Milch glatt rühren.\nBirne würfeln, unterheben, 10 g gehackte Mandeln darüber. Bei Bedarf etwas Zimt."],
            ['Vollkorn-Toast mit Avocado & Ei', '1 Scheibe Vollkorntoast, ½ Avocado, 1 Ei', 380, 2,
                "Toast rösten, ½ Avocado mit einer Gabel direkt darauf zerdrücken, salzen.\nEi 6–7 Minuten kochen (wachsweich), halbieren und darauflegen. Chiliflocken passen gut."],
        ],
        'mittags' => [
            ['Salatbowl mit Hähnchenbrust', '150 g Hähnchenbrust, großer gemischter Salat, ½ Gurke, 100 g Kirschtomaten, Joghurtdressing', 450, 2,
                "150 g Hähnchenbrust in Streifen schneiden, würzen und in 1 TL Öl 5–6 Minuten scharf anbraten.\nDressing: 100 g Joghurt, 1 TL Senf, Zitronensaft, Salz, Pfeffer.\nÜber Salat, ½ Gurke und 100 g Kirschtomaten geben, Hähnchen darauf."],
            ['Linsensuppe (Meal-Prep)', 'Pro Portion ca. 80 g rote Linsen — großer Topf für 3–4 Portionen', 400, 3,
                "1 Zwiebel, 2 Karotten und 1 Stange Sellerie anschwitzen, 250 g rote Linsen und 1 l Gemüsebrühe dazu.\n15 Minuten köcheln, mit Kreuzkümmel, Paprika und einem Spritzer Essig abschmecken.\nErgibt 3–4 Portionen — den Rest portionsweise einfrieren."],
            ['Vollkornwrap mit Thunfisch & Gemüse', '1 Vollkornwrap, 1 Dose Thunfisch in Wasser (130 g), 2 EL Joghurt, ½ Paprika, Salat', 420, 1,
                "Thunfisch abtropfen, mit 2 EL Joghurt, Salz und Pfeffer vermengen.\nWrap mit Salat, ½ Paprika in Streifen und der Thunfischcreme belegen, fest einrollen, halbieren."],
            ['Quinoa-Bowl mit Kichererbsen & Ofengemüse', '60 g Quinoa (roh), 120 g Kichererbsen (½ Dose), 250 g Ofengemüse, 1 EL Olivenöl', 480, 3,
                "60 g Quinoa nach Packung kochen (oder vom Vortag).\n250 g Ofengemüse (Paprika, Zucchini, Süßkartoffel bei 200 °C, 25 Min) und 120 g abgespülte Kichererbsen darauf.\nDressing: 1 EL Olivenöl, Zitrone, Kreuzkümmel."],
            ['Gemüseomelett mit Vollkornbrot', '3 Eier, 200 g Gemüse nach Vorrat, 1 Scheibe Vollkornbrot (50 g), 1 TL Öl', 400, 2,
                "200 g Gemüse (Paprika, Zucchini, Champignons) klein schneiden und in 1 TL Öl anbraten.\n3 verquirlte Eier darüber, stocken lassen. Mit einer Scheibe Vollkornbrot servieren."],
            ['Couscous-Salat mit Feta & Minze', '60 g Couscous (roh), ½ Gurke, 1 Tomate, 40 g Feta, Minze, 1 EL Olivenöl', 430, 2,
                "60 g Couscous mit 60 ml kochender Brühe übergießen, 5 Minuten quellen lassen, auflockern.\n½ Gurke, 1 Tomate, 40 g Feta und Minze unterheben, mit Zitrone und 1 EL Olivenöl anmachen.\nDoppelte Menge machen — morgen ist das Mittagessen schon fertig."],
            ['Gefüllte Süßkartoffel mit Kräuterquark', '1 mittlere Süßkartoffel (250 g), 150 g Magerquark, Schnittlauch', 380, 2,
                "Süßkartoffel mehrfach einstechen, 8–10 Minuten in der Mikrowelle (oder 45 Min bei 200 °C im Ofen).\nLängs aufschneiden, 150 g Quark mit Schnittlauch, Salz und Pfeffer hineingeben."],
            ['Asia-Gemüsepfanne mit Tofu & Reis', '150 g Tofu, 300 g TK-Wokgemüse, 60 g Reis (roh), 2 EL Sojasauce, 1 TL Öl', 450, 3,
                "150 g Tofu würfeln, trocken tupfen und in 1 TL Öl knusprig anbraten, herausnehmen.\n300 g Wokgemüse scharf anbraten, Tofu zurück, mit 2 EL Sojasauce, Ingwer und Knoblauch ablöschen.\nMit 60 g Reis (Rohgewicht) servieren."],
            ['Kichererbsensalat mit Paprika & Zitrone', '1 Dose Kichererbsen (240 g Abtropfgewicht), 1 Paprika, ½ Gurke, 1 EL Olivenöl, Zitrone', 400, 1,
                "Kichererbsen abspülen und abtropfen lassen.\n1 Paprika und ½ Gurke würfeln, alles mit 1 EL Olivenöl, viel Zitronensaft, Salz und Kreuzkümmel vermengen.\nOptional Petersilie oder 30 g Feta (+80 kcal)."],
            ['Vollkornpasta mit Tomaten-Linsen-Sugo', '70 g Vollkornpasta (roh), 200 ml passierte Tomaten, 50 g rote Linsen, 1 Zwiebel', 480, 2,
                "1 Zwiebel und Knoblauch anschwitzen, 200 ml passierte Tomaten und 50 g rote Linsen dazu.\n15 Minuten köcheln, mit Basilikum, Salz und einer Prise Zucker abschmecken.\nÜber 70 g Vollkornpasta (Rohgewicht) geben."],
        ],
        'abends' => [
            ['Ofenlachs mit Brokkoli', '150 g Lachsfilet, 300 g Brokkoli, 1 TL Öl, ½ Zitrone', 420, 2,
                "Ofen auf 200 °C. 300 g Brokkoliröschen mit 1 TL Öl aufs Blech, 10 Minuten vorgaren.\n150 g Lachs dazulegen, salzen, Zitronenscheiben darauf, weitere 12–15 Minuten backen."],
            ['Puten-Gemüse-Pfanne', '150 g Putenbrust, 1 Zucchini, 1 Paprika, 2 EL Sojasauce, 1 TL Öl', 380, 2,
                "150 g Putenstreifen in 1 TL Öl scharf anbraten, herausnehmen.\nZucchini und Paprika in derselben Pfanne braten, Pute zurückgeben, mit 2 EL Sojasauce und Pfeffer abschmecken."],
            ['Griechischer Salat mit Feta', '2 Tomaten, ½ Gurke, ½ rote Zwiebel, 8 Oliven, 50 g Feta, 1 EL Olivenöl', 350, 1,
                "2 Tomaten und ½ Gurke grob würfeln, ½ rote Zwiebel in Ringen dazu.\n8 Oliven und 50 g Feta darüber, mit 1 EL Olivenöl, Oregano und wenig Salz anmachen.\nDazu passt eine kleine Scheibe Vollkornbrot (+110 kcal)."],
            ['Zucchini-Puffer mit Kräuterquark', '2 Zucchini (400 g), 1 Ei, 2 EL Mehl, 150 g Quark, 1 EL Öl zum Braten', 330, 3,
                "400 g Zucchini grob raspeln, kräftig salzen, 10 Minuten ziehen lassen und gut ausdrücken.\nMit 1 Ei und 2 EL Mehl mischen, portionsweise in 1 EL Öl goldbraun braten.\nDazu 150 g Quark mit Kräutern, Salz und Knoblauch."],
            ['Blumenkohlreis mit Garnelen', '½ Blumenkohl (300 g), 150 g Garnelen, 2 Knoblauchzehen, 1 TL Öl, Zitrone', 320, 2,
                "300 g Blumenkohl im Mixer zu „Reis\" pulsen (oder fertig gekauft).\n150 g Garnelen mit Knoblauch in 1 TL Öl 2–3 Minuten braten, herausnehmen.\nBlumenkohlreis 5 Minuten in der Pfanne garen, Garnelen zurück, Zitrone darüber."],
            ['Tomatensuppe mit weißen Bohnen', '400 ml passierte Tomaten, 1 Dose weiße Bohnen (240 g), 1 Zwiebel, Basilikum', 300, 2,
                "1 Zwiebel und Knoblauch anschwitzen, 400 ml passierte Tomaten und einen Schuss Brühe dazu, 10 Minuten köcheln.\n240 g abgespülte Bohnen dazugeben und erwärmen. Mit Basilikum, Salz und Pfeffer abschmecken."],
            ['Gefüllte Paprika mit magerem Hack', '2 Paprika, 150 g Rinderhack (5 %), 40 g Reis (roh), 200 ml passierte Tomaten', 450, 3,
                "150 g Hack mit 1 Zwiebel anbraten, 40 g gekochten Reis und 1 EL Tomatenmark untermischen, würzen.\n2 Paprika halbieren, entkernen, füllen und bei 180 °C ca. 30 Minuten backen.\n200 ml passierte Tomaten mit ins Blech — ergibt die Sauce."],
            ['Shakshuka', '2 Eier, 1 Paprika, 1 Zwiebel, 200 ml passierte Tomaten, Kreuzkümmel, 1 TL Öl', 350, 2,
                "1 Zwiebel und 1 Paprika in 1 TL Öl weich dünsten, Knoblauch, Kreuzkümmel und Paprikapulver dazu.\n200 ml passierte Tomaten angießen, 10 Minuten einkochen.\nZwei Mulden formen, Eier hineinschlagen, Deckel drauf und stocken lassen."],
            ['Halloumi mit Ofengemüse', '80 g Halloumi, 400 g Gemüse nach Saison, 1 EL Olivenöl', 400, 2,
                "400 g Gemüse (Zucchini, Paprika, Champignons) mit 1 EL Öl bei 200 °C ca. 25 Minuten rösten.\n80 g Halloumi in Scheiben ohne Fett goldbraun braten und darauflegen. Zitrone darüber."],
            ['Kürbissuppe mit Ingwer', '½ Hokkaido (400 g), 1 Zwiebel, 2 cm Ingwer, 50 ml Kokosmilch, 500 ml Brühe', 280, 2,
                "400 g Hokkaido würfeln (Schale bleibt dran), mit 1 Zwiebel und 2 cm Ingwer anschwitzen.\nMit 500 ml Brühe bedecken, 15 Minuten weich kochen, pürieren.\n50 ml Kokosmilch dazu, mit Salz und Muskat abschmecken."],
        ],
        'snack' => [
            ['Apfel mit Erdnussbutter', '1 Apfel, 1 EL Erdnussbutter (15 g, ohne Zucker)', 180, 1,
                "Apfel in Spalten schneiden, in die Erdnussbutter dippen.\n1 EL (15 g) abmessen — nicht aus dem Glas löffeln."],
            ['Handvoll Mandeln', '25 g (ca. 20 Stück)', 150, 1,
                "25 g sind etwa 20 Mandeln. In eine kleine Schale, Tüte zurück in den Schrank.\nAus der Tüte essen endet nie bei 25 g."],
            ['Gemüsesticks mit Hummus', '1 Karotte, ½ Gurke, ½ Paprika, 2 EL Hummus (40 g)', 150, 1,
                "Gemüse in Sticks schneiden — geht auch am Sonntag auf Vorrat für 2–3 Tage (in Wasser im Kühlschrank).\n2 EL Hummus (40 g) in eine Schale, nicht der ganze Becher auf den Tisch."],
            ['Skyr pur mit Zimt', '150 g Skyr, Zimt', 100, 1,
                "150 g Skyr in eine Schale, großzügig Zimt darüber.\nKlingt langweilig, hält aber locker bis zur nächsten Mahlzeit."],
            ['2 harte Eier', '2 Eier (Größe M)', 140, 1,
                "Eier 9–10 Minuten kochen, abschrecken.\nGeschält halten sie im Kühlschrank 3–4 Tage — der schnellste Protein-Snack, den es gibt."],
            ['Edamame', '150 g TK-Edamame (mit Schote gewogen)', 120, 1,
                "150 g TK-Edamame 5 Minuten in Salzwasser kochen, abgießen.\nMit etwas grobem Salz aus der Schote essen — dauert lange, sättigt gut."],
            ['Magerquark mit Kakao', '150 g Magerquark, 1 TL Backkakao (ungesüßt), Süßstoff', 130, 1,
                "150 g Magerquark mit 1 TL Backkakao und Süßstoff (oder 1 TL Honig, +20 kcal) verrühren.\nSchmeckt wie Schokocreme, ist aber ein Protein-Snack — der Abend-Retter."],
            ['Banane', '1 Banane', 100, 1,
                "Keine Zubereitung — das ist der Punkt.\nVor der Kickr-Einheit essen, dann ist der Süßhunger danach kleiner."],
            ['Dunkle Schokolade (85 %)', '2 Stücke (10 g), 85 % Kakao', 110, 1,
                "2 Stücke (10 g) abbrechen, Tafel zurück in den Schrank, hinsetzen, langsam essen.\nDas ist „Naschen: bewusst\" — genau so ist es gedacht."],
            ['Proteinriegel', '1 Riegel (< 200 kcal, > 15 g Protein)', 200, 1,
                "Für die Schublade oder die Trikottasche.\nBeim Kauf auf unter 200 kcal und über 15 g Protein achten — viele „Fitness\"-Riegel sind Süßigkeiten."],
        ],
    ];

    public function run(): void
    {
        foreach (User::all() as $user) {
            $existing = Recipe::where('user_id', $user->id);

            if ($existing->exists()) {
                // Refresh seed texts by name, but never touch personal
                // ratings or recipes the user created themselves
                $this->refreshSeedTexts($user);

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

    private function refreshSeedTexts(User $user): void
    {
        foreach (self::RECIPES as $category => $recipes) {
            foreach ($recipes as [$name, $description, $kcal, $aufwand, $instructions]) {
                Recipe::where('user_id', $user->id)
                    ->where('category', $category)
                    ->where('name', $name)
                    ->update([
                        'description' => $description,
                        'instructions' => $instructions,
                        'kcal' => $kcal,
                    ]);
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
