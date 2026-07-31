<?php

namespace Tests\Feature;

use App\Models\Recipe;
use App\Models\User;
use Database\Seeders\RecipeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_ten_recipes_per_category(): void
    {
        $user = User::factory()->create();

        $this->seed(RecipeSeeder::class);

        foreach (array_keys(Recipe::CATEGORIES) as $category) {
            $this->assertSame(10, Recipe::where('user_id', $user->id)->where('category', $category)->count());
        }

        // Seeder must not duplicate an existing collection
        $this->seed(RecipeSeeder::class);
        $this->assertSame(40, Recipe::where('user_id', $user->id)->count());
    }

    public function test_recipe_can_be_added_shown_edited_and_removed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/rezepte', [
            'name' => 'Ofengemüse mit Feta',
            'category' => 'abends',
            'kcal' => 380,
            'instructions' => "Gemüse schneiden.\nBei 200 Grad rösten.",
        ]);

        $recipe = Recipe::firstWhere('name', 'Ofengemüse mit Feta');
        $this->assertSame('abends', $recipe->category);

        $this->actingAs($user)->get("/rezepte/{$recipe->id}")
            ->assertOk()
            ->assertSee('Zubereitung')
            ->assertSee('Bei 200 Grad rösten.');

        $this->actingAs($user)->put("/rezepte/{$recipe->id}", [
            'name' => 'Ofengemüse mit Halloumi',
            'category' => 'abends',
            'kcal' => 420,
            'instructions' => $recipe->instructions,
        ])->assertRedirect("/rezepte/{$recipe->id}");

        $this->assertSame('Ofengemüse mit Halloumi', $recipe->fresh()->name);
        $this->assertSame(420, $recipe->fresh()->kcal);

        $this->actingAs($user)->delete("/rezepte/{$recipe->id}");
        $this->assertSame(0, Recipe::count());
    }

    public function test_seeded_recipes_have_instructions(): void
    {
        User::factory()->create();
        $this->seed(RecipeSeeder::class);

        $this->assertSame(0, Recipe::whereNull('instructions')->count());
    }

    public function test_rating_can_be_set_and_toggled_off(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::create([
            'user_id' => $user->id,
            'category' => 'snack',
            'name' => 'Banane',
        ]);

        $this->actingAs($user)->post("/rezepte/{$recipe->id}/bewertung", [
            'dimension' => 'geschmack',
            'stars' => 4,
        ]);
        $this->assertSame(4, $recipe->fresh()->stars_geschmack);

        $this->actingAs($user)->post("/rezepte/{$recipe->id}/bewertung", [
            'dimension' => 'geschmack',
            'stars' => 4,
        ]);
        $this->assertNull($recipe->fresh()->stars_geschmack);
    }

    public function test_invalid_dimension_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recipe::create([
            'user_id' => $user->id,
            'category' => 'snack',
            'name' => 'Banane',
        ]);

        $this->actingAs($user)->post("/rezepte/{$recipe->id}/bewertung", [
            'dimension' => 'preis',
            'stars' => 4,
        ])->assertSessionHasErrors('dimension');
    }

    public function test_foreign_recipe_cannot_be_rated_or_deleted(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $recipe = Recipe::create([
            'user_id' => $other->id,
            'category' => 'snack',
            'name' => 'Banane',
        ]);

        $this->actingAs($user)->post("/rezepte/{$recipe->id}/bewertung", [
            'dimension' => 'geschmack',
            'stars' => 4,
        ])->assertForbidden();

        $this->actingAs($user)->delete("/rezepte/{$recipe->id}")->assertForbidden();
    }
}
