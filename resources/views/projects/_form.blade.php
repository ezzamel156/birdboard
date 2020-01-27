@csrf

<div class="field mb-6">
    <label class="label text-sm sb-2 block" for="title">Title</label>
    <div class="control">
        <input 
            class="input bg-transparent border border-grey-light rounded p-2 text-xs w-full" 
            type="text" 
            name="title" 
            id="title"
            placeholder="My next awesome project"
            value="{{ $project->title }}"
            required>
    </div>
</div>

<div class="field mb-6">
    <label class="label text-sm sb-2 block" for="description">Description</label>
    <div class="control">
        <textarea 
            placeholder="I should start learning javascript properly"
            class="textarea bg-transparent border border-grey-light rounded p-2 text-xs w-full" 
            name="description" 
            id="description"
            required>{{ $project->description }}
        </textarea>
    </div>
</div>

<div>
    <button class="button mr-2" type="submit">{{$project->exists ? 'Update Project' : 'Create Project'}}</button>
    <a href=" {{ $project->path() }} ">Cancel</a>
</div>

@if ($errors->any())
    <div class="field mt-6">    
            @foreach ($errors->all() as $error)
                <li class="text-sm text-red"> {{ $error }} </li>            
            @endforeach    
    </div>
@endif
