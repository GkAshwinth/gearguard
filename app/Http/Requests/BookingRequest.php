<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * BookingRequest — validates booking form submissions.
 *
 * SECURITY: after_or_equal:today prevents backdated bookings.
 * after_or_equal:start_date prevents inverted date ranges (a common
 * manipulation attempt). These rules are enforced server-side regardless
 * of what the frontend sends.
 */
class BookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'equipment_id' => ['required', 'exists:equipment,id'],
            'start_date'   => ['required', 'date', 'after_or_equal:today'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after_or_equal' => 'The end date must be on or after the start date.',
            'start_date.after_or_equal' => 'Rental must start from today or a future date.',
            'equipment_id.exists'      => 'The selected equipment does not exist.',
        ];
    }
}
