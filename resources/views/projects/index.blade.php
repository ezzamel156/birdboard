@extends('layouts.app')

@section('content')
    <header class="flex items-center mb-3 py-3">
        <div class="flex justify-between items-end w-full">
            <h2 class="text-muted no-underline text-base font-light">My Projects</h2>
            <a class="button" href="/projects/create" @click.prevent="$modal.show('new-project')">Create a Project</a>
        </div>
    </header>
        
    <main class="lg:flex lg:flex-wrap -mx-3">
        @forelse ($projects as $project)
            <div class="lg:w-1/3 px-3 pb-6">
                @include('projects.card')           
            </div>            
        @empty        
            <div>No projects yet.</div>            
        @endforelse
    </main>
    
    <new-project-modal></new-project-modal>

@endsection
