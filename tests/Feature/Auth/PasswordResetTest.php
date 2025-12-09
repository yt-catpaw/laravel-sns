<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ゲストはパスワードリセット画面を表示できる()
    {
        $response = $this->get('/password/reset');

        $response->assertStatus(200);
        $response->assertSee('パスワードをリセット');
    }

    #[Test]
    public function 不正なメールアドレスではバリデーションエラーになる()
    {
        $response = $this->from('/password/reset')->post('/password/reset', [
            'email' => 'not-an-email',
        ]);

        $response->assertRedirect('/password/reset');
        $response->assertSessionHasErrors('email');
    }
}
