@extends('layouts.app')

@section('content')

    <header class="flex items-center mb-3 py-3">
        <div class="flex justify-between items-end w-full">
            <p class="text-grey text-sm font-normal">
                <a href="/projects" class="text-grey text-sm font-normal no-underline">My Projects</a> / {{ $project->title }}
            </p>

            <div class="flex items-center">
                @foreach ($project->members as $member)
                    <img 
                        src="https://s3.amazonaws.com/laracasts/images/default-square-avatar.jpg" 
                        alt=" {{ $member->name }}'s avatar " 
                        class="rounded-full w-8 mr-2">
                @endforeach

                <img 
                    src="https://s3.amazonaws.com/laracasts/images/default-square-avatar.jpg" 
                    alt=" {{ $project->owner->name }}'s avatar " 
                    class="rounded-full w-8 mr-2">

                <a class="button ml-4" href="{{$project->path().'/edit'}}">Edit Project</a>
            </div>
            
        </div>
    </header>

    <main>
        <div class="lg:flex -mx-3">
            <div class="lg:w-3/4 px-3 mb-6">
                <div class="mb-8">
                    <h2 class="text-grey text-lg font-normal mb-3">Tasks</h2>
                    {{-- tasks --}}

                    @foreach ($project->tasks as $task)
                    
                        <div class="card mb-3"> 
                            <form action="{{ $task->path() }}" method="POST">
                            
                                @method('PATCH')
                                @csrf

                                <div class="flex items-center">
                                    <input class="w-full {{ $task->completed ? 'text-grey' : '' }}" type="text" name="body" id="task" value="{{ $task->body }}" >
                                    <input type="checkbox" name="completed" id="completed" onchange="this.form.submit()" {{ $task->completed ? 'checked' : '' }} >
                                </div>                                
                            </form>
                        </div>                        
            
                    @endforeach

                    <div class="card mb-3">
                        <form action="{{ $project->path().'/tasks' }}" method="POST">

                            @csrf
                            <input class="w-full" type="text" name="body" id="task" placeholder="Add a new task...">

                        </form>
                    </div>
                </div>

                <div>
                    <h2 class="text-grey text-lg font-normal mb-3">General Notes</h2>
                    {{-- general notes --}}

                    <form action="{{ $project->path() }}" method="POST">
                        @csrf
                        @method('PATCH')
                    
                        <textarea 
                            name="notes"
                            placeholder="Anything special that you want to make a note of?" 
                            class="card w-full" 
                            style="min-height:200px"
                        >{{$project->notes}}</textarea>

                        <button type="submit" class="button">Save</button>
                    </form> 
                    
                    @include('errors')
                </div>
            </div>

            <div class="lg:w-1/4 px-3 lg:py-8">
                @include('projects.card')           
                @include('projects.activity.card')

                @can('manage', $project)
                    @include('projects.invite')   
                @endcan                                           
            </div>
        </div>
    </main>
    
@endsection