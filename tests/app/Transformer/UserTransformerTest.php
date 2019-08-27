<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\User;
use App\Transformer\UserTransformer;
use League\Fractal\TransformerAbstract;

class UserTransformerTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_be_initialized()
    {
        $subject = new UserTransformer();
        $this->assertInstanceOf(TransformerAbstract::class, $subject);
    }

    /** @test */
    public function it_transform_a_user_model()
    {
        $user = factory(User::class)->create();
        $subject = new UserTransformer();

        $transform = $subject->transform($user);

        $this->assertArrayHasKey('id', $transform);
        $this->assertArrayHasKey('name', $transform);
        $this->assertArrayHasKey('email', $transform);
        $this->assertArrayHasKey('created', $transform);
        $this->assertArrayHasKey('updated', $transform);
    }
}
