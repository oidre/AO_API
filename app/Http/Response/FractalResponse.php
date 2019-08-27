<?php

namespace App\Http\Response;

use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\TransformerAbstract;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class FractalResponse
{
  /** @var Manager */
  private $manager;

  /** @var SerializerAbstract */
  private $serializer;

  public function __construct(Manager $manager, SerializerAbstract $serializer)
  {
    $this->manager = $manager;
    $this->serializer = $serializer;
    $this->manager->setSerializer($serializer);
  }

  public function item($data, TransformerAbstract $transformer, $resourceKey = null)
  {
    return $this->createDataArray(new Item($data, $transformer, $resourceKey));
  }

  public function collection($data, TransformerAbstract $transformer, $resourceKey = null)
  {
    return $this->createDataArray(new Collection($data, $transformer, $resourceKey));
  }

  public function paginate($data, TransformerAbstract $transformer, $paginator, $resourceKey = null)
  {
    $resource = new Collection($data, $transformer, $resourceKey);
    $resource->setPaginator(new IlluminatePaginatorAdapter($paginator));
    
    return $this->createDataArray($resource);
  }

  public function createData($data)
  {
    return $this->manager->createData($data)->toArray();
  }

  public function createDataArray(ResourceInterface $resource)
  {
    return $this->manager->createData($resource)->toArray();
  }
}