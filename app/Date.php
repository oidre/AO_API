<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Date extends Model
{
    protected $fillable = ['full_date', 'month', 'month_name', 'year'];

    public function reports()
    {
        return $this->hasMany('App\Report');
    }
}
