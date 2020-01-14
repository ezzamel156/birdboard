@extends('layouts.app')

@section('content')
        
    <h1>Birdboard</h1>

    <a href="/projects/create">Create a Project</a>

    <ul>

        @forelse ($projects as $project)

        <li>
            
            <a href="{{ $project->path() }}"> {{ $project->title }} </a>
        
        </li>

        @empty
        
            <li>No projects yet.</li>
            
        @endforelse

    </ul>

@endsection
