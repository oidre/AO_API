<?php

namespace App\Transformer;

use App\Report;
use League\Fractal\TransformerAbstract;

class ReportTransformer extends TransformerAbstract
{
  /**
   * Transform a Report model into an array
   * 
   * @param Report $report
   * @return array
   */
  public function transform(Report $report)
  {
    return $report->toArray();
  }
}