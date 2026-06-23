<?php
// Booking controller file

namespace App\Http\Controllers\Api\V1;

use App\Exceptions\BookingException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(private BookingService $bookingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $bookings = $this->bookingService->getUserBookings($request->user(), $perPage);

        return response()->json([
            'success' => true,
            'message' => 'Bookings retrieved successfully.',
            'data' => BookingResource::collection($bookings),
            'meta' => [
                'pagination' => [
                    'total' => $bookings->total(),
                    'count' => $bookings->count(),
                    'per_page' => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'total_pages' => $bookings->lastPage(),
                ],
            ],
        ]);
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        try {
            $booking = $this->bookingService->createBooking($request->user(), $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully.',
                'data' => new BookingResource($booking->load(['venue', 'slot'])),
                'meta' => null,
            ], 201);
        } catch (BookingException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }

    public function show(Booking $booking): JsonResponse
    {
        $this->authorize('view', $booking);

        return response()->json([
            'success' => true,
            'message' => 'Booking retrieved successfully.',
            'data' => new BookingResource($booking->load(['venue', 'slot'])),
            'meta' => null,
        ]);
    }

    public function cancel(Booking $booking, Request $request): JsonResponse
    {
        $this->authorize('cancel', $booking);

        try {
            $booking = $this->bookingService->cancelBooking($request->user(), $booking, $request->input('reason'));

            return response()->json([
                'success' => true,
                'message' => 'Booking cancelled successfully.',
                'data' => new BookingResource($booking->load(['venue', 'slot'])),
                'meta' => null,
            ]);
        } catch (BookingException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }

    public function checkIn(Booking $booking): JsonResponse
    {
        $this->authorize('checkIn', $booking);

        try {
            $booking = $this->bookingService->checkInBooking($booking);

            return response()->json([
                'success' => true,
                'message' => 'Booking checked in successfully.',
                'data' => new BookingResource($booking->load(['venue', 'slot'])),
                'meta' => null,
            ]);
        } catch (BookingException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'meta' => null,
            ], 422);
        }
    }
}
