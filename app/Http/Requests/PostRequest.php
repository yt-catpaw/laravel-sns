<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
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
            'tweet' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'tweet.required' => '投稿内容を入力してください。',
            'tweet.string' => '投稿内容は文字列で入力してください。',
            'tweet.max' => '投稿内容は255文字以内で入力してください。',

            'image.image' => '画像ファイルを選択してください。',
            'image.max' => '画像ファイルは4MB以内でアップロードしてください。',
        ];
    }
}
