<?php

namespace Tests\Feature;

use App\Mail\PasswordResetLinkMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PasswordResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ユーザーが存在する場合はトークンを保存してメールを送信する(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('password.reset.send'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $user->email,
        ]);

        $record = PasswordResetToken::where('email', $user->email)->firstOrFail();

        $this->assertSame(64, strlen($record->token));
        $this->assertNull($record->used_at);
        $this->assertTrue($record->expires_at->isFuture());

        Mail::assertSent(PasswordResetLinkMail::class, function ($mail) use ($user, $record) {
            return $mail->hasTo($user->email)
                && str_contains($mail->resetUrl, $record->token);
        });
    }

    #[Test]
    public function ユーザーが存在しない場合はトークンを保存せずメールも送らない(): void
    {
        Mail::fake();

        $response = $this->post(route('password.reset.send'), [
            'email' => 'no-user@example.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('status');

        $this->assertDatabaseMissing('password_reset_tokens', [
            'email' => 'no-user@example.com',
        ]);

        Mail::assertNothingSent();
    }

    #[Test]
    public function 同一メールで再発行した場合は未使用トークンが1つだけになる(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->post(route('password.reset.send'), ['email' => $user->email]);
        $first = PasswordResetToken::where('email', $user->email)->firstOrFail();

        $this->post(route('password.reset.send'), ['email' => $user->email]);

        $tokens = PasswordResetToken::where('email', $user->email)->get();

        $this->assertSame(1, $tokens->whereNull('used_at')->count());
        $second = $tokens->first();

        $this->assertNotSame($first->token, $second->token);
    }

    #[Test]
    public function メールアドレス形式が不正な場合はバリデーションエラーになる(): void
    {
        Mail::fake();

        $response = $this->from(route('password.reset.show'))
            ->post(route('password.reset.send'), [
                'email' => 'invalid-email',
            ]);

        $response->assertRedirect(route('password.reset.show'));
        $response->assertSessionHasErrors(['email']);

        Mail::assertNothingSent();
    }
}
