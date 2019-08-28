<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['module_id', 'project_id', 'date_id', 'application_object_used'];

    public function module()
    {
        return $this->belongsTo('App\Module');
    }

    public function project()
    {
        return $this->belongsTo('App\Project');
    }

    public function date()
    {
        return $this->belongsTo('App\Date');
    }
}
