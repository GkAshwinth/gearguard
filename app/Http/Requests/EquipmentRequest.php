<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * EquipmentRequest — validates and sanitises all equipment form submissions.
 *
 * SECURITY: Using Form Request classes instead of inline validation ensures
 * all input is validated before it reaches the controller. Laravel's
 * validator also auto-escapes strings, preventing SQL injection via
 * Eloquent's parameterised queries.
 */
class EquipmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only owners can create/edit equipment
        return $this->user()?->isOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:Cameras,Lenses,Lighting,Audio,Drones,Other'],
            'daily_rate'  => ['required', 'numeric', 'min:1', 'max:999999'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:4096'],
            'status'      => ['sometimes', 'in:available,rented,maintenance'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.in'    => 'Please select a valid category.',
            'image.max'      => 'Image must be smaller than 4MB.',
            'daily_rate.min' => 'Daily rate must be at least LKR 1.',
        ];
    }
}
