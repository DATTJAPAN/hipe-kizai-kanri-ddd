<?php

declare(strict_types=1);

namespace App\Domains\System\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SystemLoginRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['nullable', 'email', 'max:254'],
            'password' => ['nullable'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
