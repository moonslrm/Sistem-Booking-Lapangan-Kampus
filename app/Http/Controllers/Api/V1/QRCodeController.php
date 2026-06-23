<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\QRValidationResource;
use App\Models\Booking;
use App\Services\QRCodeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class QRCodeController extends Controller
{
    public function show($bookingId): JsonResponse
    {
        $booking = Booking::query()->with('qrCode')->findOrFail($bookingId);

        if ($booking->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (! $booking->qrCode?->qr_image_path) {
            return response()->json(['success' => false, 'message' => 'QR not generated yet'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_image_url' => \Illuminate\Support\Facades\Storage::disk('public')->url($booking->qrCode->qr_image_path),
            ],
        ]);
    }

    public function validateQr(Request $request, QRCodeService $qrCodeService): JsonResponse
    {
        $payload = $request->input('payload');

        if (! $payload) {
            return response()->json(['success' => false, 'message' => 'Payload QR tidak ditemukan'], 422);
        }

        $result = $qrCodeService->validateQR($payload, $request->user()->id);

        if (! $result['valid']) {
            return response()->json(['success' => false, 'message' => $result['message']], 422);
        }

        return response()->json(['success' => true, 'message' => $result['message'], 'data' => new QRValidationResource($result['booking'])]);
    }
}
