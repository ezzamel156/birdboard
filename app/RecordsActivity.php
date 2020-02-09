<?php

namespace App;

trait RecordsActivity
{    
    /**
     * The project's old attributes
     * 
     * @var array
     */
    public $oldAttributes = [];

    /**
     * Boot the trait
     */
    public static function bootRecordsActivity()
    {       
        foreach(self::recordableEvents() as $event) {
            static::$event(function ($model) use ($event) {                

                $model->recordActivity($model->activityDescription($event));
            });
            
            if($event === 'updated') {
                static::updating(function ($model) {
                    $model->oldAttributes = $model->getOriginal();
                });
            }
        }
    }

    protected function activityDescription($description)
    {                
        return "{$description}_" . strtolower(class_basename($this)); // created_task
    }

    protected static function recordableEvents()
    {
        if(isset(static::$recordableEvents)) {
            return static::$recordableEvents;
        }
        
        return ['created', 'updated'];
    }

    /**
     * Record activity for a model
     * 
     * @param string $description
     */
    public function recordActivity($description)
    {
        //dd($this->owner_id);
        $this->activities()->create([
            'user_id' => ($this->project ?? $this)->owner->id,
            'description' => $description,
            'changes' =>  $this->activityChanges(),
            'project_id' => class_basename($this) === 'Project' ? $this->id : $this->project_id
        ]);
    }

    public function activities()
    {
        return $this->morphMany(Activity::class, 'subject')->latest();
    }  

    /** 
     * Get changes of a model
     * 
     * @return array
    */
    protected function activityChanges()
    {
        if( $this->wasChanged() ) {
            return [
                'before' => array_except(array_diff($this->oldAttributes, $this->getAttributes()), 'updated_at'),
                'after' => array_except($this->getChanges(), 'updated_at')
            ];
        }
    }
}