<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeadControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Optional: Create fixed lead statuses if you want specific ones
        LeadStatus::factory()->create(['name' => 'New Lead']);
        LeadStatus::factory()->create(['name' => 'In Progress']);
        LeadStatus::factory()->create(['name' => 'Completed']);
    }

    /** @test */
    public function it_can_list_leads()
    {
        // Create 10 leads with associated lead statuses
        Lead::factory()->count(10)->create();

        $response = $this->get('/api/leads');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => ['*' => ['id', 'name', 'email', 'phone', 'lead_status']],
        ]);
    }

    /** @test */
    public function it_can_create_a_lead()
    {
        $leadStatus = LeadStatus::factory()->create();

        $leadData = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'phone' => '123-456-7890',
            'lead_status_id' => $leadStatus->id,
        ];

        $response = $this->post('/api/leads', $leadData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('leads', ['email' => 'johndoe@example.com']);
    }

    /** @test */
    public function it_can_update_a_lead()
    {
        $lead = Lead::factory()->create();

        $updatedData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '987-654-3210',
            'lead_status_id' => $lead->lead_status_id,
        ];

        $response = $this->put("/api/leads/{$lead->id}", $updatedData);

        $response->assertStatus(200);
        $this->assertDatabaseHas('leads', ['name' => 'Updated Name']);
    }

    /** @test */
    public function it_can_delete_a_lead()
    {
        $lead = Lead::factory()->create();

        $response = $this->delete("/api/leads/{$lead->id}");

        $response->assertStatus(200);
        $this->assertDeleted($lead);
    }
}
