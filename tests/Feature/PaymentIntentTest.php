<?php

namespace Tests\Feature;

use App\Models\User;
use Mockery;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class PaymentIntentTest extends TestCase
{
    private User $user;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->make(['id' => 1]);
    }

    #[Test]
    public function 単発プランでclient_secretを返す(): void
    {
        $mock = Mockery::mock('alias:Stripe\PaymentIntent');
        $mock->shouldReceive('create')
            ->once()
            ->andReturn((object) ['client_secret' => 'pi_secret_dummy']);

        $response = $this->actingAs($this->user)
            ->postJson(route('payment.intent'), ['plan_type' => 'one_time']);

        $response->assertOk()
            ->assertJson(['clientSecret' => 'pi_secret_dummy']);
    }

    #[Test]
    public function 未対応プランなら422を返す(): void
    {
        $mock = Mockery::mock('alias:Stripe\PaymentIntent');
        $mock->shouldReceive('create')->never();

        $response = $this->actingAs($this->user)
            ->postJson(route('payment.intent'), ['plan_type' => 'unknown_plan']);

        $response->assertStatus(422)
            ->assertJsonStructure(['error']);
    }
}
