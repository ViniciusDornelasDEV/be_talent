<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexCitiesByStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'state_id' => $this->route('state_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'state_id' => 'required|integer|exists:states,id',
        ];
    }
}
