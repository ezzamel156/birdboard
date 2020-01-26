<?php

namespace  Tests\Setup;

use App\Project;
use App\Task;
use App\User;

class ProjectFactory
{
    protected $tasksCount = 0;

    protected $owner; 

    public function withTasks($count)
    {
       $this->tasksCount = $count;

       return $this;
    }

    public function create()
    {
        $project = factory(Project::class)->create([
            'owner_id' => $this->owner ?? factory(User::class)
        ]);

        factory(Task::class, $this->tasksCount)->create([
            'project_id' => $project->id
        ]);

        return $project;
    }

    public function ownedBy($user)
    {
        $this->owner = $user;

        return $this;   
    }
}