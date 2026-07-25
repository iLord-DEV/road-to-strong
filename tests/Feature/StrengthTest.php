<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\StrengthEntry;
use App\Models\StrengthSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StrengthTest extends TestCase
{
    use RefreshDatabase;

    private function makeExercise(User $user, string $name = 'Kniebeuge', string $workout = 'A'): Exercise
    {
        return Exercise::create([
            'user_id' => $user->id,
            'name' => $name,
            'workout' => $workout,
            'position' => 1,
        ]);
    }

    public function test_exercise_can_be_created(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/kraft/uebungen', ['name' => 'Bankdrücken', 'workout' => 'B'])
            ->assertRedirect('/kraft/uebungen');

        $this->assertDatabaseHas('exercises', ['name' => 'Bankdrücken', 'workout' => 'B']);
    }

    public function test_session_with_entries_is_stored(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise($user);

        $this->actingAs($user)->post('/kraft', [
            'workout' => 'A',
            'performed_at' => today()->toDateString(),
            'entries' => [
                ['exercise_id' => $exercise->id, 'weight_kg' => 60, 'reps' => 8, 'sets' => 3],
            ],
        ])->assertRedirect('/kraft');

        $session = StrengthSession::firstWhere('user_id', $user->id);
        $this->assertSame('A', $session->workout);
        $this->assertSame(60.0, $session->entries->first()->weight_kg);
    }

    public function test_exercises_without_reps_are_skipped(): void
    {
        $user = User::factory()->create();
        $done = $this->makeExercise($user, 'Kniebeuge');
        $skipped = $this->makeExercise($user, 'Rudern');

        $this->actingAs($user)->post('/kraft', [
            'workout' => 'A',
            'performed_at' => today()->toDateString(),
            'entries' => [
                ['exercise_id' => $done->id, 'weight_kg' => 60, 'reps' => 8, 'sets' => 3],
                ['exercise_id' => $skipped->id, 'weight_kg' => null, 'reps' => null, 'sets' => null],
            ],
        ]);

        $this->assertSame(1, StrengthEntry::count());
        $this->assertSame($done->id, StrengthEntry::first()->exercise_id);
    }

    public function test_empty_session_is_rejected(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise($user);

        $this->actingAs($user)->post('/kraft', [
            'workout' => 'A',
            'performed_at' => today()->toDateString(),
            'entries' => [
                ['exercise_id' => $exercise->id, 'weight_kg' => null, 'reps' => null, 'sets' => null],
            ],
        ])->assertSessionHasErrors('entries');

        $this->assertSame(0, StrengthSession::count());
    }

    public function test_form_prefills_last_values(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise($user);

        $session = StrengthSession::create([
            'user_id' => $user->id,
            'workout' => 'A',
            'performed_at' => today()->subDays(3),
        ]);
        $session->entries()->create([
            'exercise_id' => $exercise->id,
            'weight_kg' => 62.5,
            'reps' => 8,
            'sets' => 3,
        ]);

        $this->actingAs($user)->get('/kraft/neu?workout=A')
            ->assertOk()
            ->assertSee('value="62.5"', false)
            ->assertSee('Letztes Mal: 62,5 kg × 8 × 3');
    }

    public function test_deleted_exercise_keeps_history_and_leaves_form(): void
    {
        $user = User::factory()->create();
        $exercise = $this->makeExercise($user);

        $session = StrengthSession::create([
            'user_id' => $user->id,
            'workout' => 'A',
            'performed_at' => today()->subDays(3),
        ]);
        $session->entries()->create([
            'exercise_id' => $exercise->id,
            'weight_kg' => 60,
            'reps' => 8,
            'sets' => 3,
        ]);

        $this->actingAs($user)->delete("/kraft/uebungen/{$exercise->id}");

        $this->assertSoftDeleted('exercises', ['id' => $exercise->id]);
        $this->assertSame(1, StrengthEntry::count());
        // No active exercises left in workout A -> entry form 404s
        $this->actingAs($user)->get('/kraft/neu?workout=A')->assertNotFound();
    }

    public function test_foreign_exercise_is_rejected(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $foreign = $this->makeExercise($other);

        $this->actingAs($user)->post('/kraft', [
            'workout' => 'A',
            'performed_at' => today()->toDateString(),
            'entries' => [
                ['exercise_id' => $foreign->id, 'weight_kg' => 60, 'reps' => 8, 'sets' => 3],
            ],
        ])->assertSessionHasErrors();

        $this->actingAs($user)->delete("/kraft/uebungen/{$foreign->id}")->assertForbidden();
    }
}
