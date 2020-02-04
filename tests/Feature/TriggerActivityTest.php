<?php

namespace Tests\Feature;

use App\Task;
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

        $this->assertCount(1, $project->activities);

        tap($project->activities->last(), function($activity) {            
            $this->assertEquals('created_project', $activity->first()->description);
            $this->assertNull($activity->changes);
        });
    }

    /** @test */
    
    public function updating_a_project()
    {
        $project = ProjectFactory::create();
        $originalTitle = $project->title;

        $project->update(['title' => 'Changed']);
        $this->assertCount(2, $project->activities);

        tap($project->activities->last(), function($activity) use ($originalTitle){
            $this->assertEquals('updated_project', $activity->description);

            $expected = [
                'before' => ['title' => $originalTitle ],
                'after' => ['title' => 'Changed' ]
            ];
            $this->assertEquals($expected, $activity->changes);

        });
        
    }

    /** @test */
    
    public function creating_a_new_task()
    {
        $project = ProjectFactory::create();

        $project->addTask('Some task');

        $this->assertCount(2, $project->activities);

        tap($project->activities->last(), function($activity){
            // dd($activity->toArray());
            $this->assertEquals('created_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
            $this->assertEquals('Some task', $activity->subject->body);
        });
        
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
        
        tap($project->activities->last(), function($activity){
            // dd($activity->toArray());
            $this->assertEquals('completed_task', $activity->description);
            $this->assertInstanceOf(Task::class, $activity->subject);
        });
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
