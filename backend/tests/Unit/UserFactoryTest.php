<?php

namespace Tests\Unit;

use App\Models\User;
use Database\Factories\UserFactory;
use Mockery;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * @return void
     */
    public function test_create_model_by_factory_success(): void
    {
        $userFactoryMock = Mockery::mock(UserFactory::class);

        $userFactoryMock->shouldReceive('create')
            ->once()
            ->andReturn(new User());

        $this->instance(UserFactory::class, $userFactoryMock);

        $factory = UserFactory::new();
        $model = $userFactoryMock->create();

        $this->assertInstanceOf(UserFactory::class, $factory);
        $this->assertInstanceOf(User::class, $model);
    }
}
