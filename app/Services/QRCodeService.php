<?php

namespace App\Services;

use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\BookingQrCode;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class QRCodeService
{
    public function generateToken(Booking $booking): string
    {
        $payload = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'expires_at' => $booking->booking_date->toDateString() . ' ' . $booking->end_time,
        ];

        return hash_hmac('sha256', json_encode($payload), Config::get('app.key'));
    }

    public function generateQRImage(Booking $booking): string
    {
        $token = $this->generateToken($booking);
        $payload = [
            'booking_id' => $booking->id,
            'booking_code' => $booking->booking_code,
            'token' => $token,
        ];

        $payloadJson = json_encode($payload);

        $options = new QROptions([
            'version' => 5,
            'outputType' => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => false,
        ]);

        $qrCode = new QRCode($options);
        $image = $qrCode->render($payloadJson);

        $path = 'qr-codes/' . $booking->id . '.png';
        Storage::disk('public')->put($path, $image);

        BookingQrCode::updateOrCreate(
            ['booking_id' => $booking->id],
            [
                'token' => $token,
                'qr_image_path' => $path,
                'is_used' => false,
            ]
        );

        return Storage::disk('public')->url($path);
    }

    public function validateQR(string $qrPayload, ?int $koorlapUserId = null): array
    {
        $payload = json_decode($qrPayload, true);

        if (! is_array($payload) || empty($payload['booking_id']) || empty($payload['token'])) {
            return ['valid' => false, 'message' => 'QR tidak valid'];
        }

        $qrCode = BookingQrCode::where('booking_id', $payload['booking_id'])
            ->where('token', $payload['token'])
            ->first();

        if (! $qrCode) {
            return ['valid' => false, 'message' => 'QR tidak valid'];
        }

        $booking = $qrCode->booking()->with(['user', 'venue', 'slot'])->first();

        if (! $booking) {
            return ['valid' => false, 'message' => 'QR tidak valid'];
        }

        if ($booking->status !== 'confirmed') {
            return ['valid' => false, 'message' => 'Booking tidak aktif (status: ' . $booking->status . ')'];
        }

        if ($qrCode->is_used) {
            return ['valid' => false, 'message' => 'QR sudah pernah digunakan pada ' . $qrCode->scanned_at?->toDateTimeString()];
        }

        $startAt = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->start_time);
        $endAt = Carbon::parse($booking->booking_date->toDateString() . ' ' . $booking->end_time);
        $now = Carbon::now();

        if ($now->lt($startAt->copy()->subMinutes(30))) {
            return ['valid' => false, 'message' => 'Terlalu awal untuk check-in'];
        }

        if ($now->gt($endAt)) {
            return ['valid' => false, 'message' => 'Sesi sudah berakhir'];
        }

        $qrCode->update([
            'is_used' => true,
            'scanned_at' => now(),
            'scanned_by' => $koorlapUserId,
        ]);

        $booking->status = 'checked_in';
        $booking->save();

        return [
            'valid' => true,
            'message' => 'QR valid, check-in berhasil',
            'booking' => new BookingResource($booking->load(['user', 'venue', 'slot', 'qrCode'])),
        ];
    }
}
