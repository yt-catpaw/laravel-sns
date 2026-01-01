<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:254'],
            'token' => ['required', 'string', 'size:64'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'メールアドレスを入力してください。',
            'email.email' => 'メールアドレスの形式が正しくありません。',
            'email.max' => 'メールアドレスが長すぎます。',

            'token.required' => 'トークンが取得できませんでした。もう一度やり直してください。',
            'token.string' => 'トークンの形式が不正です。',
            'token.size' => 'トークンの長さが不正です。',

            'password.required' => '新しいパスワードを入力してください。',
            'password.string' => 'パスワードは文字列で入力してください。',
            'password.min' => 'パスワードは8文字以上で入力してください。',
            'password.confirmed' => 'パスワード（確認）が一致しません。',
        ];
    }
}
