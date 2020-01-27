<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $guarded = [];

    protected $touches = ['project'];

    protected $casts = [
        'completed' => 'boolean'
    ];

    protected static function boot()
    {
        // for education purposes, we are using model event. To be more consistent, should use observer since
        // for Project model we are using observer.
        parent::boot();
        static::created(function ($task){
            // Activity::create([
            //     'project_id' => $task->project->id,
            //     'description' => 'created_task'
            // ]);
            $task->project->recordActivity('created_task');
        });

        // static::updated(function ($task){
        //     if(! $task->completed) return;

        //     $task->project->recordActivity('completed_task');

        //     // Activity::create([
        //     //     'project_id' => $task->project->id,
        //     //     'description' => 'completed_task'
        //     // ]);
        // });
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function path()
    {
        return "{$this->project->path()}/tasks/{$this->id}";
    }

    public function complete()
    {
        $this->update(['completed' => true]);

        $this->project->recordActivity('completed_task');
    }

    public function incomplete()
    {
        $this->update(['completed' => false]);
    }
}
