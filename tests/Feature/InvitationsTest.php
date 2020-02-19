<?php

namespace Tests\Feature;

use App\User;
use Facades\Tests\Setup\ProjectFactory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvitationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    
    public function non_owners_may_not_invite_users()
    {   
        $project = ProjectFactory::create();
        $user = factory(User::class)->create();       

        $assertInvitationForbidden =  function() use ($user, $project) {
            $this->actingAs($user)
                ->post($project->path().'/invitations')
                ->assertStatus(403);
        };
        
        $assertInvitationForbidden();

        $project->invite($user);

        $assertInvitationForbidden();
    }

    /** @test */
    
    public function invited_users_may_update_project_details()
    {
        $project = ProjectFactory::create();

        $project->invite($newUser = factory('App\User')->create());

        $this->signIn($newUser);
        $this->post(action('ProjectTasksController@store', $project), $task = ['body' => 'Body text']);

        $this->assertDatabaseHas('tasks', $task);
    }

    /** @test */
    
    public function a_project_owner_can_invite_a_user()
    {    
        $project = ProjectFactory::create();

        $userToInvite = factory(User::class)->create();

        $this->actingAs($project->owner)->post($project->path().'/invitations', [
            'email' => $userToInvite->email
        ])
        ->assertRedirect($project->path()); //invite user

        $this->assertTrue($project->members->contains($userToInvite));
    }

    /** @test */
    
    public function email_address_must_be_under_a_birdboard_account()
    {
        $project = ProjectFactory::create();

        $this->actingAs($project->owner)->post($project->path().'/invitations', [
            'email' => 'notauser@example.com'
        ])
        ->assertSessionHasErrors([
            'email' => 'The user you are inviting must have a Birdboard account.'
        ], null, 'invitations');
    }
}
