<?php

namespace App\Http\Requests\Diary;

use App\Models\Diary;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'content' => ['required', 'max:' . Diary::CONTENT_MAX_LENGTH, 'min:' . Diary::CONTENT_MIN_LENGTH],
            'image' => ['nullable', 'image'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'content' => '本文',
            'image' => '画像',
        ];
    }
}
