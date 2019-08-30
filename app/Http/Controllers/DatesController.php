<?php

namespace App\Http\Controllers;

use App\Date;
use Illuminate\Http\Request;

class DatesController extends Controller {

  public function index() {
    return Date::all();
  }
}
