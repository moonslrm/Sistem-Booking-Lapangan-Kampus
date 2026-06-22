<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use App\Services\VenueService;
use App\Services\SlotAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class VenueController extends Controller
{
    public function __construct(
        private VenueService $venueService,
        private SlotAvailabilityService $availabilityService,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $venues = $this->venueService->getAllVenues($request->only(['sport_type', 'is_active', 'search', 'per_page']));

        return response()->json([
            'success' => true,
            'message' => 'Venues retrieved successfully.',
            'data' => VenueResource::collection($venues),
            'meta' => [
                'pagination' => [
                    'total' => $venues->total(),
                    'count' => $venues->count(),
                    'per_page' => $venues->perPage(),
                    'current_page' => $venues->currentPage(),
                    'total_pages' => $venues->lastPage(),
                ],
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $venue = $this->venueService->getVenueDetail((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Venue detail retrieved successfully.',
            'data' => new VenueResource($venue),
            'meta' => null,
        ]);
    }

    public function slots($id, Request $request): JsonResponse
    {
        $attributes = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        $venue = $this->venueService->getVenueDetail((int) $id);
        $cacheKey = $this->availabilityService->cacheKey($venue->id, $attributes['date']);
        $slots = Cache::remember($cacheKey, 30, fn () => $this->availabilityService->getAvailableSlots($venue, $attributes['date']));

        return response()->json([
            'success' => true,
            'message' => 'Venue slots retrieved successfully.',
            'data' => [
                'venue_id' => $venue->id,
                'date' => $attributes['date'],
                'slots' => $slots,
            ],
            'meta' => null,
        ]);
    }

    public function reviews($id): JsonResponse
    {
        $venue = $this->venueService->getVenueDetail((int) $id);

        return response()->json([
            'success' => true,
            'message' => 'Venue reviews retrieved successfully.',
            'data' => $venue->reviews->map(fn ($review) => [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'created_at' => $review->created_at?->toDateTimeString(),
            ]),
            'meta' => null,
        ]);
    }
}
