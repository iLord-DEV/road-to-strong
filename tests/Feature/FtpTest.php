<?php

namespace Tests\Feature;

use App\Models\BodyMeasurement;
use App\Models\FtpEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_ftp_entry_can_be_added_and_removed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/ftp', ['watts' => 245, 'tested_at' => today()->toDateString()])
            ->assertRedirect('/verlauf');

        $entry = FtpEntry::firstWhere('user_id', $user->id);
        $this->assertSame(245, $entry->watts);

        $this->actingAs($user)->delete("/ftp/{$entry->id}")->assertRedirect('/verlauf');
        $this->assertSame(0, FtpEntry::count());
    }

    public function test_dashboard_shows_ftp_per_kg(): void
    {
        $user = User::factory()->create();

        BodyMeasurement::create([
            'user_id' => $user->id,
            'withings_grpid' => 1,
            'measured_at' => now()->subDay(),
            'weight_kg' => 78.4,
            'raw' => [],
        ]);
        FtpEntry::create([
            'user_id' => $user->id,
            'watts' => 245,
            'tested_at' => today()->subWeek(),
        ]);

        // 245 / 78.4 = 3.1249... -> 3,12 W/kg
        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('3,12')
            ->assertSee('FTP 245 W');
    }

    public function test_foreign_ftp_entry_cannot_be_deleted(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();

        $entry = FtpEntry::create([
            'user_id' => $other->id,
            'watts' => 200,
            'tested_at' => today(),
        ]);

        $this->actingAs($user)->delete("/ftp/{$entry->id}")->assertForbidden();
        $this->assertSame(1, FtpEntry::count());
    }
}
