<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $task;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'user@gmail.com',
        ]);

        $this->actingAs($this->user);

        $this->task = [
            "name" => "test",
            "description" => "this is the first tast",
            "start_time" => "2024-02-03",
            "end_time" => "2024-10-03",
        ];
    }

    public function test_that_a_user_can_create_a_task(): void
    {
        $response = $this->postJson('/api/v1/tasks', $this->task);

        $response->assertStatus(201);
        $this->assertDatabaseHas('tasks', $this->task);
    }

    public function test_that_a_user_can_update_a_task(): void
    {
      
        $createdTask = Task::factory()->create($this->task);

        $updatedTaskData = [
            "name" => "updated test",
            "description" => "updated description",
            "start_time" => "2024-02-10",
            "end_time" => "2024-10-10",
        ];

        $response = $this->putJson('/api/v1/tasks/'.$createdTask->id, $updatedTaskData);

        $response->assertStatus(200); 

        $this->assertDatabaseHas('tasks', $updatedTaskData);
    }

    public function test_that_a_user_can_read_all_task(): void
    {
        $createdTask = Task::factory()->create($this->task);

        $response = $this->getJson('/api/v1/tasks/');

        $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => $createdTask->name,
            'description' => $createdTask->description,
            'start_time' => $createdTask->start_time,
            'end_time' => $createdTask->end_time,
        ]);
    }
    public function test_that_a_user_can_read_a_particular_task(): void
    {
        $createdTask = Task::factory()->create($this->task);

        $response = $this->getJson('/api/v1/tasks/'.$createdTask->id);

        $response->assertStatus(200)
        ->assertJsonFragment([
            'name' => $createdTask->name,
            'description' => $createdTask->description,
            'start_time' => $createdTask->start_time,
            'end_time' => $createdTask->end_time,
        ]);
    }

    public function test_that_a_user_can_delete_a_task(): void
    {
        $createdTask = Task::factory()->create($this->task);

        $response = $this->deleteJson('/api/v1/tasks/'.$createdTask->id);

        $response->assertStatus(200);

        $this->assertDatabaseMissing('tasks', $this->task);
    }
}
