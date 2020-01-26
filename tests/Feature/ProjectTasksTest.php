<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ProjectFactory;
use App\Project;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTasksTest extends TestCase
{

    Use WithFaker, RefreshDatabase;


    /** @test */
    
    public function guests_cannot_add_tasks_to_projects()
    {

        $project = factory('App\Project')->create();

        $this->post($project->path().'/tasks')
            ->assertRedirect('login');
            
    }

    /** @test */
    
    public function a_project_can_have_tasks()
    {
        $project = ProjectFactory::create();

        $attributes = [
            'body' => 'test task'
        ];

        $this->ActingAs($project->owner)
            ->post($project->path(). '/tasks', $attributes)
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('tasks', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['body']);

    }

    /** @test */
    
    public function a_task_can_be_updated()
    {
        $project = ProjectFactory::withTasks(1)->create();
        
        $this->ActingAs($project->owner)
            ->patch($project->tasks->first()->path(), [
            'body' => 'changed',
            'completed' => true
        ]);

        $this->assertDatabaseHas('tasks', [
            'body' => 'changed',
            'completed' => true
        ]);
    }

    /** @test */
    
    public function a_task_requires_a_body()
    {        
        $project = ProjectFactory::create();

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->ActingAs($project->owner)
            ->post($project->path().'/tasks', $attributes)
            ->assertSessionHasErrors('body');

    }

    /** @test */
    
    public function only_project_owner_can_add_task()
    {

        $this->signIn();

        $project = ProjectFactory::create();

        $attributes = factory('App\Task')->raw();

        $this->post($project->path().'/tasks', $attributes)
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $attributes);

    }

    /** @test */
    
    public function only_project_owner_can_update_task()
    {
        $this->signIn();

        $project = ProjectFactory::withTasks(1)->create();

        $this->patch($project->tasks->first()->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);
    }
}
