<?php

namespace Tests\Feature;

use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ActivityFeedTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    
    public function creating_a_project_generates_activity()
    {
        $project = ProjectFactory::create();

        $this->assertEquals('created', $project->activities->first()->description);
        $this->assertCount(1, $project->activities);
    }

    /** @test */
    
    public function updating_a_project_generates_activity()
    {
        $project = ProjectFactory::create();

        $project->update(['title' => 'Changed']);
        $this->assertCount(2, $project->activities);
        $this->assertEquals('updated', $project->activities->last()->description);
    }
}
