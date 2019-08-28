<?php

namespace App\Transformer;

use App\Project;
use League\Fractal\TransformerAbstract;

class ProjectTransformer extends TransformerAbstract
{
  /**
   * Transform a Project model into an array
   * 
   * @param Project $project
   * @return array
   */
  public function transform(project $project)
  {
    return [
      'id' => $project->id,
      'name' => $project->name,
      'created' => $project->created_at->toIso8601String(),
      'updated' => $project->updated_at->toIso8601String(),
    ];
  }
}