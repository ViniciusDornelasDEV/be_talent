<?php

declare(strict_types=1);

namespace Modules\Order\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client'              => ['required', 'array'],
            'client.email'        => ['nullable', 'email', 'required_without:client.name'],
            'client.name'         => ['nullable', 'string', 'required_without:client.email'],

            'items'               => ['required', 'array', 'min:1'],
            'items.*.product_id'  => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity'    => ['required', 'integer', 'min:1'],

            'card'                => ['required', 'array'],
            'card.number'         => ['required', 'digits_between:12,19'],
            'card.cvv'            => ['required', 'digits_between:3,4'],
        ];
    }
}

