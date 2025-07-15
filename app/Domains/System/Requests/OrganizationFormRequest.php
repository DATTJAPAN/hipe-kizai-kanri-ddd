<?php

declare(strict_types=1);

namespace App\Domains\System\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:254',
            'domain' => 'required|string|max:254|unique:organizations,domain',
            'business_email' => 'required|email|max:254',
        ];

        match (mb_strtoupper($this->method())) {
            'POST' => $this->postRules(rules: $rules),
            'PUT', 'PATCH' => $this->updateRules(rules: $rules),
            default => null,
        };

        return $rules;
    }

    private function postRules(array &$rules): void
    {
        // TODO:
    }

    private function updateRules(array &$rules): void
    {
        // TODO:
    }
}
