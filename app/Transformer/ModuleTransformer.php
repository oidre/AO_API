<?php

namespace App\Transformer;

use App\Module;
use League\Fractal\TransformerAbstract;

class ModuleTransformer extends TransformerAbstract
{
  /**
   * Transform a Module model into an array
   * 
   * @param Module $module
   * @return array
   */
  public function transform(Module $module)
  {
    return [
      'id' => $module->id,
      'name' => $module->name,
      'application_object' => $module->application_object,
      'created' => $module->created_at->toIso8601String(),
      'updated' => $module->updated_at->toIso8601String(),
    ];
  }
}