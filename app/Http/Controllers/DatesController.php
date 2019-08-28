<?php

namespace App\Http\Controllers;

use App\Date;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DatesController extends Controller {
  private $request;

  public function __construct(Request $request) {
    $this->request = $request;
    $this->model = new Date;
  }

  public function store() {
    Carbon::setLocale('id');
    $this->validate($this->request, [
        'date' => 'required|date'
    ]);

    $now = Carbon::parse($this->request->date);

    // Check if this month already exist
    $monthExist = Date::where('month', $now->month)->where('year', $now->year)->first();

    if ($monthExist === null) {
      $newDate = Date::create([
        'full_date' => $now->format('Y-m-d'),
        'month' => $now->month,
        'month_name' => $now->monthName,
        'year' => $now->year,
      ]);

      return response()->json($newDate, 201);
    }
    return response()->json([
      'message' => 'Dates already created',
    ], 200);
  }
}
