<?php

namespace App\Query;

use Unlu\Laravel\Api\QueryBuilder;

class ReportQueryBuilder extends QueryBuilder
{
  public function filterByMonth($query, $month)
  {
    return $query->whereHas('date', function($q) use ($month) {
      return $q->where('month', $month);
    });
  }

  public function filterByYear($query, $year)
  {
    return $query->whereHas('date', function($q) use ($year) {
      return $q->where('year', $year);
    });
  }
}