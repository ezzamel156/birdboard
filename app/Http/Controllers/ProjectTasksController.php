<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;

class ProjectTasksController extends Controller
{
    public function store(Project $project)
    {

        $attributes = request()->validate([
            'body' => 'required'
        ]);
        
        $project->addTask($attributes['body']);

        return redirect($project->path());

    }
}
