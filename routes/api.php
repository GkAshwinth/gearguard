<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;

/*
|--------------------------------------------------------------------------
| API Routes — GearGuard
|--------------------------------------------------------------------------
|
| All routes (except /login) are protected by Laravel Sanctum.
| Clients must POST to /api/login with their credentials to receive
| a Bearer token, then include it in all subsequent requests:
|
|   Authorization: Bearer {token}
|
| SECURITY: Sanctum validates the token on every request, checks token
| abilities (scopes), and automatically revokes expired tokens.
| Tokens are hashed in the database — if the DB is compromised, raw
| tokens cannot be extracted.
|
*/

Route::prefix('v1')->group(function () {
    // Public: token issuance
    Route::post('/login', [ApiController::class, 'login']);

    // Sanctum-protected endpoints
    Route::middleware('auth:sanctum')->group(function () {

        // Auth
        Route::post('/logout', [ApiController::class, 'logout']);
        Route::get('/user', fn (Request $r) => response()->json($r->user()));

        // Equipment
        Route::get('/equipment',         [ApiController::class, 'equipmentIndex']);
        Route::get('/equipment/{equipment}', [ApiController::class, 'equipmentShow']);
        Route::post('/equipment',        [ApiController::class, 'equipmentStore']);

        // Bookings
        Route::get('/bookings',          [ApiController::class, 'bookingsIndex']);
        Route::post('/bookings',         [ApiController::class, 'bookingsStore']);

        // Dashboard metrics (owner only)
        Route::get('/dashboard',         [ApiController::class, 'dashboard']);
    });
});
