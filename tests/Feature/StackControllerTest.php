<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\Stack;

class StackControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run migrations for tests
        Artisan::call('migrate');
    }

    /** @test */
    public function it_can_add_to_stack()
    {
        $response = $this->postJson('/api/stack', ['value' => 'Hello']);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Value added to stack']);

        $this->assertDatabaseHas('stacks', [
            'value' => 'Hello'
        ]);
    }

    /** @test */
    public function it_can_get_from_stack()
    {
        Stack::create(['value' => 'First']);
        Stack::create(['value' => 'Second']);
        Stack::create(['value' => 'Third']);

        $response = $this->getJson('/api/stack');

        $response->assertStatus(200)
                 ->assertJson(['value' => 'Third']);

        $this->assertDatabaseHas('stacks', [
            'value' => 'Third',
            'deleted_at' => now()->format('Y-m-d H:i:s')
        ]);

        $this->assertDatabaseMissing('stacks', [
            'value' => 'Third',
            'deleted_at' => null
        ]);
    }
}
