<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TriggerActivityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    
    public function creating_a_project()
    {
        $project = ProjectFactory::create();

        $this->assertEquals('created', $project->activities->first()->description);
        $this->assertCount(1, $project->activities);
    }

    /** @test */
    
    public function updating_a_project()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'Changed']);
        $this->assertCount(2, $project->activities);
        $this->assertEquals('updated', $project->activities->last()->description);
    }

    /** @test */
    
    public function creating_a_new_task()
    {
        $project = ProjectFactory::create();

        $project->addTask('Some task');

        $this->assertCount(2, $project->activities);
        $this->assertEquals('created_task', $project->activities->last()->description);
    }

    /** @test */
    
    public function completing_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        // $this->ActingAs($project->owner)
        //     ->patch($project->tasks[0]->path(), [
        //         'body' => 'foobar',
        //         'completed' => true
        //     ]);

        $project->tasks->last()->complete();

        $this->assertCount(3, $project->activities);
        $this->assertEquals('completed_task', $project->activities->last()->description);
    }

    /** @test */
    
    public function incompleting_a_task()
    {
        $project = ProjectFactory::withTasks(1)->create();

        // $this->ActingAs($project->owner)
        //     ->patch($project->tasks[0]->path(), [
        //         'body' => 'foobar',
        //         'completed' => true
        //     ]);

        $project->tasks->last()->complete();
        $this->assertCount(3, $project->activities);

        $project->tasks->last()->incomplete();
        $project->refresh();
        $this->assertCount(4, $project->activities);

        $this->assertEquals('incompleted_task', $project->activities->last()->description);
    }

    /** @test */
    
    public function deleting_a_test()
    {
        $project = ProjectFactory::withTasks(1)->create();

        $project->tasks->last()->delete();

        $this->assertCount(3, $project->activities);
    }
}
