<?php

declare(strict_types=1);

namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Product\Models\Product;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \Modules\User\Models\User|null $user */
        $user = $this->user();

        return $user?->can('update', $this->route('product') ?? Product::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name'   => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
        ];
    }
}

