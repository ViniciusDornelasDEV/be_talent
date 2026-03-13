<?php

declare(strict_types=1);

namespace Modules\Gateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Gateway\Models\Gateway;

class ToggleGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $gateway = $this->route('gateway');
        return $user?->can('toggle', $gateway) ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}

