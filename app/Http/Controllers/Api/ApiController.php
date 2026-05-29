<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Cache;
use App\Traits\ApiResponses;

/**
 * GearGuard REST API Controller
 *
 * All routes (except login) are protected by Laravel Sanctum token authentication.
 * Tokens are issued at POST /api/login and must be sent as:
 *   Authorization: Bearer {token}
 *
 * SECURITY: Sanctum validates the token on every request via the auth:sanctum middleware.
 * Token abilities restrict what each token can do (read vs write).
 */
class ApiController extends Controller
{
    use ApiResponses;

    // ── Authentication ─────────────────────────────────────────────────────

    /**
     * POST /api/login
     * Issue a Sanctum personal access token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['sometimes', 'string', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Issue token with abilities based on role
        $abilities = $user->isOwner()
            ? ['read:equipment', 'write:equipment', 'read:bookings', 'write:bookings']
            : ['read:equipment', 'read:bookings', 'write:bookings'];

        $token = $user->createToken(
            $request->device_name ?? 'api-token',
            $abilities,
            now()->addDays(30)
        );

        return $this->success('Login successful.', [
            'token'  => $token->plainTextToken,
            'user'   => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'role'  => $user->role,
            ],
            'abilities' => $abilities,
        ]);
    }

    /**
     * POST /api/logout
     * Revoke the current token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Token revoked successfully.');
    }

    // ── Equipment Endpoints ────────────────────────────────────────────────

    /**
     * GET /api/equipment
     * List all available equipment (paginated).
     */
    public function equipmentIndex(Request $request): JsonResponse
    {
        // Generate a unique cache key based on the request parameters
        $cacheKey = 'api_equipment_' . md5(json_encode($request->all()));

        // Cache the API response for 5 minutes (300 seconds)
        $equipment = Cache::remember($cacheKey, 300, function () use ($request) {
            return \App\Models\Equipment::all();
        });

        return $this->success('Equipment retrieved successfully.', $equipment);
    }

    /**
     * GET /api/equipment/{id}
     * Show single equipment item with busy dates.
     */
    public function equipmentShow(Equipment $equipment): JsonResponse
    {
        $busyDates = $equipment->bookings()
            ->whereNotIn('status', ['rejected', 'cancelled'])
            ->where('end_date', '>=', now())
            ->get(['start_date', 'end_date']);

        return $this->success('Equipment details retrieved successfully.', [
            'equipment'  => $equipment,
            'image_url'  => $equipment->image_url,
            'busy_dates' => $busyDates,
        ]);
    }

    /**
     * POST /api/equipment  [Owner only]
     * Create new equipment item.
     */
    public function equipmentStore(Request $request): JsonResponse
    {
        // SECURITY: Check token ability
        if (!$request->user()->tokenCan('write:equipment')) {
            return $this->error('Insufficient token permissions.', 403);
        }

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'category'    => ['required', 'in:Cameras,Lenses,Lighting,Audio,Drones,Other'],
            'daily_rate'  => ['required', 'numeric', 'min:1'],
            'description' => ['nullable', 'string'],
            'image_path'  => ['nullable', 'url'],
            'status'      => ['sometimes', 'in:available,rented,maintenance'],
        ]);

        $equipment = Equipment::create($validated);

        return $this->success('Equipment created successfully.', $equipment, 201);
    }

    // ── Booking Endpoints ──────────────────────────────────────────────────

    /**
     * GET /api/bookings
     * Clients see their own bookings; owners see all bookings.
     */
    public function bookingsIndex(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->isOwner()
            ? Booking::with(['user', 'equipment'])
            : $user->bookings()->with('equipment');

        $bookings = $query->latest()->paginate(15);

        return $this->success('Bookings retrieved successfully.', [
            'bookings'   => $bookings->items(),
            'pagination' => [
                'current_page' => $bookings->currentPage(),
                'last_page'    => $bookings->lastPage(),
                'total'        => $bookings->total(),
            ],
        ]);
    }

    /**
     * POST /api/bookings
     * Create a new booking request.
     */
    public function bookingsStore(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'equipment_id' => ['required', 'exists:equipment,id'],
            'start_date'   => ['required', 'date', 'after_or_equal:today'],
            'end_date'     => ['required', 'date', 'after_or_equal:start_date'],
        ]);

        $equipment = Equipment::findOrFail($validated['equipment_id']);

        if (!$equipment->isAvailableFor($validated['start_date'], $validated['end_date'])) {
            return $this->error('Equipment is not available for the selected dates.', 422);
        }

        $days  = (new \DateTime($validated['start_date']))->diff(new \DateTime($validated['end_date']))->days + 1;
        $total = $days * $equipment->daily_rate;

        $booking = Booking::create([
            'user_id'      => $request->user()->id,
            'equipment_id' => $equipment->id,
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'total_cost'   => $total,
            'status'       => 'pending',
        ]);

        return $this->success('Booking created successfully.', $booking->load('equipment'), 201);
    }

    /**
     * GET /api/dashboard  [Owner only]
     * Business metrics summary.
     */
    public function dashboard(Request $request): JsonResponse
    {
        if (!$request->user()->isOwner()) {
            return $this->error('Owner access required.', 403);
        }

        return $this->success('Dashboard metrics retrieved successfully.', [
            'total_revenue'  => (float) Booking::where('status', 'approved')->sum('total_cost'),
            'active_rentals' => Booking::active()->count(),
            'pending_orders' => Booking::pending()->count(),
            'total_items'    => Equipment::count(),
            'overdue'        => Booking::overdue()->count(),
        ]);
    }
}
