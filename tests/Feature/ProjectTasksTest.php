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
    
    public function a_project_can_have_tasks()
    {
        //$this->withoutExceptionHandling();

        $this->signIn();

        $project = factory(Project::class)->create(['owner_id' => auth()->id()]);

       //Code below and above does the same thing

        // $project = auth()->user()->projects()->create(

        //     factory(Project::class)->raw()

        // );

        $attributes = factory('App\Task')->raw();

        $this->post($project->path(). '/tasks', $attributes)
            ->assertRedirect($project->path());

        $this->assertDatabaseHas('tasks', $attributes);

        $this->get($project->path())
            ->assertSee($attributes['body']);

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
}
