<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\KeyValueStore;
use Carbon\Carbon;

class KeyValueStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run migrations for tests
        Artisan::call('migrate');
    }

    /** @test */
    public function it_can_add_key_value_pair()
    {
        $response = $this->postJson('/api/key-value', [
            'key' => 'name',
            'value' => 'Peter',
            'ttl' => 60
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Key-Value pair added']);

        $this->assertDatabaseHas('key_value_stores', [
            'key' => 'name',
            'value' => 'Peter'
        ]);
    }

    /** @test */
    public function it_can_update_existing_key_value_pair()
    {
        KeyValueStore::create([
            'key' => 'name',
            'value' => 'Peter',
            'expires_at' => Carbon::now()->addMinutes(1),
        ]);

        $response = $this->postJson('/api/key-value', [
            'key' => 'name',
            'value' => 'Stefan',
            'ttl' => 120
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Key-Value pair added']);

        $this->assertDatabaseHas('key_value_stores', [
            'key' => 'name',
            'value' => 'Stefan'
        ]);
    }

    /** @test */
    public function it_can_get_key_value_pair()
    {
        $expiresAt = Carbon::now()->addSeconds(1);
        KeyValueStore::create([
            'key' => 'name',
            'value' => 'Peter',
            'expires_at' => $expiresAt,
        ]);

        $response = $this->getJson('/api/key-value/name');

        $response->assertStatus(200)
                 ->assertJson(['value' => 'Peter']);

        sleep(2);

        $response = $this->getJson('/api/key-value/name');
        $response->assertStatus(404)
                 ->assertJson(['value' => null]);
    }

    /** @test */
    public function it_can_delete_key_value_pair()
    {
        KeyValueStore::create([
            'key' => 'name',
            'value' => 'Peter',
            'expires_at' => Carbon::now()->addMinutes(1),
        ]);

        $response = $this->deleteJson('/api/key-value/name');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Key-Value pair deleted']);

        $this->assertDatabaseHas('key_value_stores', [
            'key' => 'name',
            'deleted_at' => now()->format('Y-m-d H:i:s')
        ]);

        $this->assertDatabaseMissing('key_value_stores', [
            'key' => 'name',
            'deleted_at' => null
        ]);
    }
}
