<?php

namespace Tests\Feature;

use App\Models\DailyLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitTest extends TestCase
{
    use RefreshDatabase;

    public function test_habit_value_is_stored_for_today(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/habit', ['field' => 'schlaf', 'value' => '4'])
            ->assertRedirect('/');

        $log = DailyLog::firstWhere('user_id', $user->id);
        $this->assertSame('4', (string) $log->schlaf);
        $this->assertSame(today()->toDateString(), $log->date->toDateString());
    }

    public function test_tapping_selected_value_clears_it(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/habit', ['field' => 'naschen', 'value' => 'keines']);
        $this->actingAs($user)->post('/habit', ['field' => 'naschen', 'value' => 'keines']);

        $this->assertNull(DailyLog::firstWhere('user_id', $user->id)->naschen);
    }

    public function test_boolean_habit_can_be_toggled(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/habit', ['field' => 'feierabend', 'value' => '1']);
        $this->assertTrue(DailyLog::firstWhere('user_id', $user->id)->feierabend);

        $this->actingAs($user)->post('/habit', ['field' => 'feierabend', 'value' => '0']);
        $this->assertFalse(DailyLog::firstWhere('user_id', $user->id)->feierabend);

        $this->actingAs($user)->post('/habit', ['field' => 'feierabend', 'value' => '0']);
        $this->assertNull(DailyLog::firstWhere('user_id', $user->id)->feierabend);
    }

    public function test_new_boolean_fields_are_storable(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/habit', ['field' => 'durchgeschlafen', 'value' => '1']);
        $this->actingAs($user)->post('/habit', ['field' => 'cannabis_vortag', 'value' => '0']);

        $log = DailyLog::firstWhere('user_id', $user->id);
        $this->assertTrue($log->durchgeschlafen);
        $this->assertFalse($log->cannabis_vortag);
    }

    public function test_mittag_is_hidden_on_weekends(): void
    {
        $user = User::factory()->create();

        $this->travelTo(now()->next('Saturday'));
        $this->actingAs($user)->get('/')->assertOk()->assertDontSee('Mittag vorbereitet');

        $this->travelTo(now()->next('Wednesday'));
        $this->actingAs($user)->get('/')->assertOk()->assertSee('Mittag vorbereitet');
    }

    public function test_habits_can_be_backfilled_within_three_days(): void
    {
        $user = User::factory()->create();
        $twoDaysAgo = today()->subDays(2);

        $this->actingAs($user)->get('/nachtrag/'.$twoDaysAgo->toDateString())
            ->assertOk()
            ->assertSee('Nachtrag');

        $this->actingAs($user)->post('/habit', [
            'field' => 'feierabend',
            'value' => '1',
            'date' => $twoDaysAgo->toDateString(),
        ]);

        $log = DailyLog::firstWhere('user_id', $user->id);
        $this->assertSame($twoDaysAgo->toDateString(), $log->date->toDateString());
        $this->assertTrue($log->feierabend);
    }

    public function test_backfill_outside_window_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/nachtrag/'.today()->subDays(4)->toDateString())->assertNotFound();
        $this->actingAs($user)->get('/nachtrag/'.today()->addDay()->toDateString())->assertNotFound();
        $this->actingAs($user)->get('/nachtrag/unsinn')->assertNotFound();

        $this->actingAs($user)->post('/habit', [
            'field' => 'feierabend',
            'value' => '1',
            'date' => today()->subDays(4)->toDateString(),
        ])->assertNotFound();

        $this->assertSame(0, DailyLog::count());
    }

    public function test_invalid_field_and_value_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/habit', ['field' => 'password', 'value' => 'x'])
            ->assertSessionHasErrors('field');

        $this->actingAs($user)
            ->post('/habit', ['field' => 'schlaf', 'value' => '9'])
            ->assertStatus(422);
    }
}
