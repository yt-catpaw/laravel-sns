<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\PasswordResetLinkRequest;
use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetLinkMail;

class PasswordResetController extends Controller
{
    public function show()
    {
        return view('password-reset.index');
    }

    public function send(PasswordResetLinkRequest $request)
    {
        $validated = $request->validated();
        $email = $validated['email'];

        $user = User::where('email', $email)->first();

        if ($user) {
              PasswordResetToken::where('email', $email)
            ->whereNull('used_at')
            ->delete();

            // 64文字トークン（DBの char(64) / string(64) 前提）
            $token = bin2hex(random_bytes(32));

             PasswordResetToken::create([
                'email' => $email,
                'token' => $token,
                'expires_at' => now()->addMinutes(30),
                'used_at' => null,
            ]);

            $resetUrl = route('password.reset.confirm', [
                'email' => $email,
                'token' => $token,
            ]);

            Mail::to($email)->send(new PasswordResetLinkMail($resetUrl));
        }
        
        return back()->with('status', 'パスワードリセットリンクを送信しました。');
    }

    public function confirm(Request $request)
    {
        return view('password-reset.confirm', [
            'email' => $request->query('email'),
            'token' => $request->query('token'),
        ]);
    }

    public function update(Request $request)
    {

    }
}
