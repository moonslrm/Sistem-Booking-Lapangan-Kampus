<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\VoucherResource;
use App\Services\VoucherService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class VoucherController
{
    public function __construct(private VoucherService $voucherService)
    {
    }

    /**
     * Validate voucher before checkout.
     */
    public function validate(Request $request): Response
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'booking_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $this->voucherService->validateVoucher(
            $validated['code'],
            $request->user(),
            (float) $validated['booking_amount']
        );

        if (! $result['valid']) {
            throw ValidationException::withMessages([
                'code' => $result['message'],
            ]);
        }

        return response([
            'valid' => true,
            'data' => [
                'discount_amount' => $result['discount_amount'],
                'final_amount' => $result['final_amount'],
            ],
        ]);
    }

    /**
     * Get active vouchers for authenticated user's role.
     */
    public function activePromos(Request $request)
    {
        $vouchers = $this->voucherService->getActiveVouchers($request->user()->role);

        return response()->json([
            'success' => true,
            'data' => VoucherResource::collection($vouchers),
            'meta' => [
                'total' => $vouchers->count(),
            ],
        ]);
    }
}
