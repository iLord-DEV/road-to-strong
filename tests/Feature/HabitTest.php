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
