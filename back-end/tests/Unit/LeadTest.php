<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Lead;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LeadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_creates_a_lead_with_valid_data()
    {
        $lead = Lead::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'lead_status_id' => 1
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function it_updates_a_lead()
    {
        $lead = Lead::factory()->create();

        $lead->update([
            'name' => 'Jane Doe'
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Jane Doe'
        ]);
    }
}
