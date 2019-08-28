<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['name'];

    public static $rules = [
        'name' => 'bail|required',
        'application_object_used' => 'bail|required|numeric|min:0'
    ];

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
