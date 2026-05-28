<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Currency implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        // Format the database float to a clean LKR string
        return 'LKR ' . number_format((float) $value, 2);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): float
    {
        // Strip out 'LKR' and commas before saving to DB
        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }
}
