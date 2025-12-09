<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ゲストは登録画面を表示できる()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('新規登録');
    }

    #[Test]
    public function ログイン済みユーザーは登録画面からリダイレクトされる()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertRedirect('/');
    }

    #[Test]
    public function 正しい情報で登録できる()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $user = User::where('email', 'new@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Test User', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function 重複メールでは登録できない()
    {
        User::factory()->create([
            'email' => 'dup@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => 'dup@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    #[Test]
    public function パスワード確認が一致しないと登録できない()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'Mismatch User',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'not-matching',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', [
            'email' => 'mismatch@example.com',
        ]);
    }
}
