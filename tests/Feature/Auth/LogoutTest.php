<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ログイン済みユーザーはログアウトできる()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $oldId = Session::getId();

        $response = $this->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();

        $this->assertNotEquals($oldId, Session::getId());
    }

    #[Test]
    public function 未ログインユーザーはログアウトできず_loginにリダイレクトされる()
    {

        $response = $this->post('/logout');

        $response->assertRedirect('/login');

        $this->assertGuest();
    }
}
