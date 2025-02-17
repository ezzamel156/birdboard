<?php

namespace Tests\Feature;

use App\Project;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ManageProjectsTest extends TestCase
{

    use WithFaker, RefreshDatabase;

    /** @test */
    
    public function guests_cannot_manage_projects()
    {
        $project = factory('App\Project')->create();
       
        $this->get('/projects')
            ->assertRedirect('login');

        $this->get('/projects/create')
            ->assertRedirect('login');

        $this->get($project->path())
            ->assertRedirect('login');

            $this->get($project->path().'/edit')
            ->assertRedirect('login');

        $this->post('/projects', $project->toArray())
            ->assertRedirect('login');
    }

    /** @test */
    
    public function a_user_can_create_a_project()
    {
        $this->signIn();

        $this->get('/projects/create')
            ->assertStatus(200);

        // $attributes = [
        //     'title' => $this->faker->sentence,
        //     'description' => $this->faker->sentence,
        //     'notes' => 'General notes here',
        // ];

        $this->followingRedirects()->post('/projects', $attributes = factory(Project::class)->raw())
            ->assertSee($attributes['title'])
            ->assertSee($attributes['description'])
            ->assertSee($attributes['notes']);
    }

    /** @test */
    
    public function tasks_can_be_included_in_new_project_creation()
    {
        $this->signIn();

        $attributes = factory(Project::class)->raw();

        $attributes['tasks'] = [
            ['body' => 'Task 1'],
            ['body' => 'Task 2'],
        ];

        $this->post('/projects', $attributes);

        $this->assertCount(2, Project::first()->tasks);
    }

    /** @test */
    
    public function a_user_can_see_all_projects_they_have_been_invited_to_on_their_dashboard()
    {
        $project = tap(ProjectFactory::create())->invite($this->signIn());

        $this->get('/projects')
            ->assertSee($project->title);        
    }

    /** @test */
    
    public function unauthorized_users_cannot_delete_a_project()
    {
        $project = ProjectFactory::create();
    
        $this->delete($project->path())
            ->assertRedirect('/login');

        $user = $this->signIn();

        $this->delete($project->path())->assertStatus(403);

        $project->invite($user);

        $this->actingAs($user)->delete($project->path())->assertStatus(403);

        //$this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /** @test */
    
    public function a_user_can_delete_a_project()
    {
        $this->withoutExceptionHandling();
        $project = ProjectFactory::create();
    
        $this->ActingAs($project->owner)
            ->delete($project->path())
            ->assertRedirect('/projects');

        $this->assertDatabaseMissing('projects', $project->only('id'));
    }

    /** @test */
    
    public function a_user_can_update_a_project()
    {
        //$this->withoutExceptionHandling();
        $project = ProjectFactory::create();
    
        $this->ActingAs($project->owner)
            ->patch($project->path(), $attributes = ['title' => 'Changed', 'description' => 'Changed' , 'notes' => 'Changed'])
            ->assertRedirect($project->path());

        $this->get($project->path().'/edit')->assertOk();

        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    
    public function a_user_can_update_a_projects_general_notes()
    {
        $project = ProjectFactory::create();
    
        $this->ActingAs($project->owner)
            ->patch($project->path(), $attributes = ['notes' => 'Changed']);

        $this->assertDatabaseHas('projects', $attributes);
    }

    /** @test */
    
    public function an_authenticated_user_cannot_view_the_projects_of_others()
    {
        $this->signIn();
        //$this->withoutExceptionHandling();
        $project = factory('App\Project')->create();

        $this->get($project->path())->assertStatus(403);
    }

    /** @test */
    
    public function an_authenticated_user_cannot_update_the_projects_of_others()
    {
        $this->signIn();
        
        $project = factory('App\Project')->create();

        $this->patch($project->path())->assertStatus(403);
    }

    /** @test */
    
    public function a_user_can_view_their_project() 
    {
        $project = ProjectFactory::create();

        $this->ActingAs($project->owner)
            ->get($project->path())
            ->assertSee($project->description)
            ->assertSee($project->title);
    }
    

    /** @test */
    
    public function a_project_requires_a_title()
    {
        $this->signIn(); //sign in use

        $attributes = factory('App\Project')->raw(['title' => '']);

        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('title');
    }

    /** @test */
    
    public function a_project_requires_a_description()
    {
        $this->signIn(); //sign in user

        $attributes = factory('App\Project')->raw(['description' => '']);

        $this->post('/projects', $attributes)
            ->assertSessionHasErrors('description');
    }
}
