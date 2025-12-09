<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    /**
     * Show the password reset request page.
     */
    public function show()
    {
        return view('password-reset.index');
    }

    /**
     * Handle password reset link request (dummy).
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // TODO: implement actual reset mail sending
        return back()->with('status', 'パスワードリセットリンクを送信しました。（ダミー処理）');
    }
}
