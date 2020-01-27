<?php

namespace Tests\Unit;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    
    public function it_has_a_path()
    {

        $task = factory('App\Task')->create();

        $this->assertEquals('/projects/'.$task->project->id.'/tasks/'.$task->id, $task->path());

    }

    /** @test */
    
    public function a_task_belongs_to_a_project()
    {
        
        $task = factory('App\Task')->create();

        $this->assertInstanceOf('App\Project', $task->project);

    }

    /** @test */
    
    public function a_task_can_be_completed()
    {
        $task = factory(Task::class)->create();

        $this->assertFalse($task->completed);

        $task->complete();

        $this->assertTrue($task->fresh()->completed);
    }
    
    /** @test */
    
    public function a_task_can_marked_as_completed()
    {
        $task = factory(Task::class)->create(['completed' => true]);

        //$task->complete();

        $this->assertTrue($task->fresh()->completed);

        $task->incomplete();

        $this->assertFalse($task->completed);

        
    }
}
