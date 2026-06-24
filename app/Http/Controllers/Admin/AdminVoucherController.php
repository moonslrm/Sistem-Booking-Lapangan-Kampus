<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Voucher\CreateVoucherRequest;
use App\Http\Resources\VoucherResource;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminVoucherController
{
    /**
     * List all vouchers with usage statistics.
     */
    public function index(Request $request): Response
    {
        $per_page = $request->query('per_page', 15);

        $vouchers = Voucher::query()
            ->withCount('usages')
            ->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => VoucherResource::collection($vouchers),
            'meta' => [
                'current_page' => $vouchers->currentPage(),
                'total' => $vouchers->total(),
                'per_page' => $vouchers->perPage(),
                'last_page' => $vouchers->lastPage(),
            ],
        ]);
    }

    /**
     * Create new voucher.
     */
    public function store(CreateVoucherRequest $request): Response
    {
        $voucher = Voucher::query()->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dibuat.',
            'data' => new VoucherResource($voucher),
        ], 201);
    }

    /**
     * Show voucher details.
     */
    public function show(Voucher $voucher): Response
    {
        $voucher->loadCount('usages');

        return response()->json([
            'success' => true,
            'data' => new VoucherResource($voucher),
        ]);
    }

    /**
     * Update voucher.
     */
    public function update(Request $request, Voucher $voucher): Response
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'discount_type' => ['sometimes', 'in:percentage,fixed'],
            'discount_value' => ['sometimes', 'numeric', 'min:0'],
            'min_booking_amount' => ['sometimes', 'numeric', 'min:0'],
            'max_discount_amount' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_total_usage' => ['sometimes', 'integer', 'min:0'],
            'max_per_user' => ['sometimes', 'integer', 'min:0'],
            'valid_from' => ['sometimes', 'date'],
            'valid_until' => ['sometimes', 'date'],
            'target_role' => ['sometimes', 'in:all,waban,umum'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        // Validate date range if both dates are updated
        if (isset($validated['valid_from']) && isset($validated['valid_until'])) {
            if ($validated['valid_until'] <= $validated['valid_from']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tanggal kadaluarsa harus lebih besar dari tanggal berlaku.',
                ], 422);
            }
        }

        $voucher->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diperbarui.',
            'data' => new VoucherResource($voucher),
        ]);
    }

    /**
     * Delete voucher.
     */
    public function destroy(Voucher $voucher): Response
    {
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus.',
        ]);
    }

    /**
     * Get usage history for specific voucher.
     */
    public function usageHistory(Voucher $voucher, Request $request): Response
    {
        $per_page = $request->query('per_page', 15);

        $usages = VoucherUsage::query()
            ->where('voucher_id', $voucher->id)
            ->with(['user', 'booking'])
            ->orderBy('used_at', 'desc')
            ->paginate($per_page);

        return response()->json([
            'success' => true,
            'data' => $usages->map(function (VoucherUsage $usage) {
                return [
                    'id' => $usage->id,
                    'user' => [
                        'id' => $usage->user->id,
                        'name' => $usage->user->name,
                        'email' => $usage->user->email,
                        'role' => $usage->user->role,
                    ],
                    'booking_id' => $usage->booking_id,
                    'discount_amount' => (float) $usage->discount_amount,
                    'used_at' => $usage->used_at->toIso8601String(),
                ];
            }),
            'meta' => [
                'current_page' => $usages->currentPage(),
                'total' => $usages->total(),
                'per_page' => $usages->perPage(),
                'last_page' => $usages->lastPage(),
            ],
        ]);
    }
}
