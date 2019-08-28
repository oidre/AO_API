<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['name', 'application_object'];

    public static $rules = [
        'name' => 'bail|required',
        'application_object' => 'bail|required|numeric|min:0'
    ];

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
