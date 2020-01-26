<?php

namespace Tests\Feature;

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
        //$this->withoutExceptionHandling();

        $this->signIn();

        $project = factory(Project::class)->create(['owner_id' => auth()->id()]);

       //Code below and above does the same thing

        // $project = auth()->user()->projects()->create(

        //     factory(Project::class)->raw()

        // );

        $attributes = [

            'body' => 'test task'
            
        ];

        $this->post($project->path(). '/tasks', $attributes)
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('tasks', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['body']);

    }

    /** @test */
    
    public function a_task_can_be_updated()
    {
        
        $this->signIn();

        $this->withoutExceptionHandling();
    
        $project = auth()->user()->projects()->create(

            factory(Project::class)->raw()

        );

        $task = $project->addTask('test task');

        $this->patch($task->path(), [

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
        
        $this->signIn();

        $project = auth()->user()->projects()->create(

            factory(Project::class)->raw()

        );

        $attributes = factory('App\Task')->raw(['body' => '']);

        $this->post($project->path().'/tasks', $attributes)->assertSessionHasErrors('body');

    }

    /** @test */
    
    public function only_project_owner_can_add_task()
    {

        $this->signIn();

        $project = factory('App\Project')->create();

        $attributes = factory('App\Task')->raw();

        $this->post($project->path().'/tasks', $attributes)
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', $attributes);

    }

    /** @test */
    
    public function only_project_owner_can_update_task()
    {

        $this->signIn();

        $project = factory('App\Project')->create();

        $task = $project->addTask('test task');

        $this->patch($task->path(), ['body' => 'changed'])
            ->assertStatus(403);

        $this->assertDatabaseMissing('tasks', ['body' => 'changed']);

    }
}
