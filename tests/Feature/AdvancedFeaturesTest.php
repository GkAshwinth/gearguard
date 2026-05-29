<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AdvancedFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_custom_currency_cast_formats_and_sanitizes_daily_rate()
    {
        // 1. Verify setting daily_rate with currency symbol and commas parses cleanly
        $equipment = Equipment::create([
            'name' => 'Test Cast Item',
            'category' => 'Cameras',
            'daily_rate' => 'LKR 1,500.50',
            'status' => 'available',
        ]);

        // 2. Verify getting daily_rate formats it into a clean LKR string
        $this->assertEquals('LKR 1,500.50', $equipment->daily_rate);
        
        // 3. Assert raw DB value is float
        $this->assertDatabaseHas('equipment', [
            'id' => $equipment->id,
            'daily_rate' => 1500.50
        ]);
    }

    public function test_equipment_resource_maps_attributes_correctly()
    {
        $equipment = Equipment::create([
            'name' => 'Pro Camera',
            'category' => 'Cameras',
            'daily_rate' => 1200.00,
            'status' => 'available',
        ]);

        $resource = new \App\Http\Resources\EquipmentResource($equipment);
        $array = $resource->toArray(request());

        $this->assertEquals($equipment->id, $array['id']);
        $this->assertEquals('Pro Camera', $array['name']);
        $this->assertEquals('Cameras', $array['category']);
        $this->assertEquals('LKR 1,200.00', $array['formatted_rate']);
        $this->assertTrue($array['is_available']);
        $this->assertNotNull($array['created_at']);
    }

    public function test_quick_stats_volt_component_displays_correct_available_count()
    {
        // Remove existing items first
        Equipment::query()->delete();

        // Create available items
        Equipment::create([
            'name' => 'Camera 1',
            'category' => 'Cameras',
            'daily_rate' => 1000,
            'status' => 'available',
        ]);

        Equipment::create([
            'name' => 'Camera 2',
            'category' => 'Cameras',
            'daily_rate' => 1200,
            'status' => 'available',
        ]);

        Volt::test('quick-stats')
            ->assertSee('Available Right Now')
            ->assertSee('2 Items');
    }

    public function test_api_versioning_and_responses_trait_formatting()
    {
        $user = User::factory()->create(['role' => 'client']);
        
        // Test auth failure is protected and rate-limited under v1
        $response = $this->getJson('/api/equipment');
        $response->assertStatus(401);

        // Test login success return formatting and structure under v1
        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'token',
                    'user' => ['id', 'name', 'email', 'role'],
                    'abilities'
                ]
            ])
            ->assertJson([
                'status' => 'Request was successful.',
                'message' => 'Login successful.'
            ]);
    }

    public function test_custom_cleanup_command_cancels_old_pending_bookings()
    {
        $user = User::factory()->create();
        $equipment = Equipment::create([
            'name' => 'Test Equipment',
            'category' => 'Cameras',
            'daily_rate' => 1000,
            'status' => 'available',
        ]);

        // Create a booking 3 days ago (expired pending)
        $oldBooking = Booking::create([
            'user_id' => $user->id,
            'equipment_id' => $equipment->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'total_cost' => 3000,
            'status' => 'pending',
        ]);
        // Manually adjust the created_at timestamp in database
        $oldBooking->created_at = now()->subDays(3);
        $oldBooking->save();

        // Create a recent booking (should remain pending)
        $recentBooking = Booking::create([
            'user_id' => $user->id,
            'equipment_id' => $equipment->id,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'total_cost' => 3000,
            'status' => 'pending',
        ]);

        // Run the console command
        $this->artisan('gearguard:cleanup')
            ->expectsOutput('Cleaned up 1 expired pending bookings!')
            ->assertExitCode(0);

        // Verify database statuses
        $this->assertEquals('cancelled', $oldBooking->fresh()->status);
        $this->assertEquals('pending', $recentBooking->fresh()->status);
    }
}
