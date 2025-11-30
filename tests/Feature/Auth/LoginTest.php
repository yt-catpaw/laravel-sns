<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ゲストはログイン画面を表示できる()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('ログイン');
    }

    #[Test]
    public function ログイン済みユーザーはログイン画面からリダイレクトされる()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect('/');
    }

    #[Test]
    public function 正しい情報でログインできる()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function 間違ったパスワードではログインできない()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->from('/login')->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
