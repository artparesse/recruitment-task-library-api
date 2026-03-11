<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'required', 'string', 'max:255'],
            'author_ids'   => ['sometimes', 'required', 'array', 'min:1'],
            'author_ids.*' => ['exists:authors,id'],
        ];
    }
}
