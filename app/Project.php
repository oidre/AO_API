<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name'];

    public static $rules = [
        'name' => 'required',
        'application_object_used' => 'required|array',
        'application_object_used.*' => 'numeric|min:0',
        'modules' => 'required|array',
        'modules.*' => 'numeric|exists:modules,id',
    ];

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
