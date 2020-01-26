@extends('layouts.app')

@section('content')
    
    <h1>Create a project</h1>

    <form method="POST" action="/projects">

        @csrf

        <div>

            <label for="title">Title</label>

            <input type="text" name="title" id="title">

        </div>

        <div>

            <label for="description">Description</label>

            <textarea  name="description" id="description"></textarea>

        </div>

        <div>

            <button type="submit">Create Project</button>

            <a href="/projects">Cancel</a>

        </div>

    </form>

@endsection