<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Project;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    
    public function it_has_a_path()
    {

        $project = factory(Project::class)->create();

        $this->assertEquals('/projects/' . $project->id, $project->path());

    }

    /** @test */
    
    public function it_belongs_to_an_owner()
    {
        
        $project = factory(Project::class)->create();

        $this->assertInstanceOf('App\User', $project->owner);

    }

    /** @test */
    
    public function it_can_add_a_task()
    {
        $project = factory(Project::class)->create();

        $task = $project->AddTask('Test tasks');

        $this->assertCount(1, $project->tasks);

        $this->assertTrue($project->tasks->contains($task));

    }

    /** @test */

    public function it_can_invite_a_user()
    {
        $project = factory(Project::class)->create();

        $project->invite($user = factory('App\User')->create());

        $this->assertTrue($project->members->contains($user));
    }
}
