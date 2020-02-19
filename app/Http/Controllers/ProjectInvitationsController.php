<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectInvitationRequest;
use App\Project;
use App\User;
use Illuminate\Http\Request;

class ProjectInvitationsController extends Controller
{
    public function store(Project $project, ProjectInvitationRequest $request)
    {
        $validated = $request->validated();
        $user = User::whereEmail($validated['email'])->first();

        $project->invite($user);

        return redirect($project->path());
    }
}
