<?php

use App\Http\Response\FractalResponse;
use League\Fractal\Manager;
use League\Fractal\Scope;
use League\Fractal\Serializer\SerializerAbstract;
use League\Fractal\TransformerAbstract;
use Mockery as m;

class FractalResponseTest extends TestCase {
  /** @test */
  public function it_can_be_initialized() {
    $manager = m::mock(Manager::class);
    $serializer = m::mock(SerializerAbstract::class);

    $manager
      ->shouldReceive('setSerializer')
      ->with($serializer)
      ->once()
      ->andReturn($manager);

    $fractal = new FractalResponse($manager, $serializer);
    $this->assertInstanceOf(FractalResponse::class, $fractal);
  }

  /** @test */
  public function it_can_transform_an_item() {
    $transformer = m::mock(TransformerAbstract::class);

    $scope = m::mock(Scope::class);
    $scope
      ->shouldReceive('toArray')
      ->once()
      ->andReturn(['foo' => 'bar']);

    $serializer = m::mock(SerializerAbstract::class);

    $manager = m::mock(Manager::class);
    $manager
      ->shouldReceive('setSerializer')
      ->with($serializer)
      ->once();

    $manager
      ->shouldReceive('createData')
      ->once()
      ->andReturn($scope);

    $subject = new FractalResponse($manager, $serializer);
    $this->assertIsArray($subject->item(['foo' => 'bar'], $transformer));
  }

  /** @test */
  public function it_can_transform_a_collection() {
    $data = [
      ['foo' => 'bar'],
      ['fizz' => 'buzz'],
    ];

    // Transformer
    $transformer = m::mock(TransformerAbstract::class);

    // Scope
    $scope = m::mock(Scope::class);
    $scope
      ->shouldReceive('toArray')
      ->once()
      ->andReturn($data);

    // Serialize
    $serializer = m::mock(SerializerAbstract::class);

    $manager = m::mock(Manager::class);
    $manager
      ->shouldReceive('setSerializer')
      ->with($serializer)
      ->once();

    $manager
      ->shouldReceive('createData')
      ->once()
      ->andReturn($scope);

    $subject = new FractalResponse($manager, $serializer);
    $this->assertIsArray($subject->collection($data, $transformer));
  }
}
