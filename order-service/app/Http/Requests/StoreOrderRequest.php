<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required', 'array', 'max:5'],
            'items.*.product_id' => ['required', 'ulid', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
        ];
    }
}
