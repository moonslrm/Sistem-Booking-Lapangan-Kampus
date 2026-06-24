<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VoucherService
{
    /**
     * Validate voucher with sequential validation (stop at first error).
     *
     * @return array{valid: bool, message?: string, discount_amount?: float, final_amount?: float}
     */
    public function validateVoucher(string $code, User $user, float $bookingAmount): array
    {
        // Step 1: Voucher exists?
        $voucher = Voucher::query()
            ->where('code', strtoupper($code))
            ->first();

        if (! $voucher) {
            return ['valid' => false, 'message' => 'Kode voucher tidak ditemukan'];
        }

        // Step 2: is_active?
        if (! $voucher->is_active) {
            return ['valid' => false, 'message' => 'Voucher tidak aktif'];
        }

        // Step 3: Date range valid?
        $now = Carbon::now();
        $validFrom = Carbon::parse($voucher->valid_from);
        $validUntil = Carbon::parse($voucher->valid_until);
        if ($now->lt($validFrom) || $now->gt($validUntil)) {
            return ['valid' => false, 'message' => 'Voucher sudah kadaluarsa atau belum berlaku'];
        }

        // Step 4: Total usage not exceeded?
        if ($voucher->max_total_usage > 0) {
            $totalUsage = VoucherUsage::query()
                ->where('voucher_id', $voucher->id)
                ->count();

            if ($totalUsage >= $voucher->max_total_usage) {
                return ['valid' => false, 'message' => 'Voucher sudah mencapai batas penggunaan'];
            }
        }

        // Step 5: User usage not exceeded?
        $userUsage = VoucherUsage::query()
            ->where('voucher_id', $voucher->id)
            ->where('user_id', $user->id)
            ->count();

        if ($voucher->max_per_user > 0 && $userUsage >= $voucher->max_per_user) {
            return ['valid' => false, 'message' => 'Kamu sudah menggunakan voucher ini'];
        }

        // Step 6: Booking amount meets minimum?
        if ($bookingAmount < $voucher->min_booking_amount) {
            return [
                'valid' => false,
                'message' => 'Minimum booking Rp ' . number_format($voucher->min_booking_amount, 0, ',', '.') . ' untuk voucher ini',
            ];
        }

        // Step 7: Role matches target?
        if ($voucher->target_role !== 'all' && $voucher->target_role !== $user->role) {
            return ['valid' => false, 'message' => 'Voucher tidak berlaku untuk tipe akunmu'];
        }

        // All validations passed - calculate discount
        $discountAmount = $this->calculateDiscount($voucher, $bookingAmount);

        return [
            'valid' => true,
            'discount_amount' => $discountAmount,
            'final_amount' => $bookingAmount - $discountAmount,
        ];
    }

    /**
     * Calculate discount amount based on voucher type.
     */
    private function calculateDiscount(Voucher $voucher, float $bookingAmount): float
    {
        if ($voucher->discount_type === 'percentage') {
            $discount = round($bookingAmount * ($voucher->discount_value / 100), 2);

            // Apply max_discount_amount cap if set
            if ($voucher->max_discount_amount > 0) {
                $discount = min($discount, $voucher->max_discount_amount);
            }
        } else {
            // Fixed discount
            $discount = (float) $voucher->discount_value;
        }

        // Ensure discount doesn't exceed booking amount
        return min($discount, $bookingAmount);
    }

    /**
     * Apply voucher to booking and create usage record.
     */
    public function applyVoucher(Voucher $voucher, User $user, Booking $booking, float $discountAmount): VoucherUsage
    {
        return VoucherUsage::query()->create([
            'voucher_id' => $voucher->id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'discount_amount' => $discountAmount,
            'used_at' => now(),
        ]);
    }

    /**
     * Get all active vouchers for user's role.
     */
    public function getActiveVouchers(?string $targetRole = null): Collection
    {
        $query = Voucher::query()
            ->where('is_active', true)
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now());

        if ($targetRole) {
            $query->where(function ($q) use ($targetRole) {
                $q->where('target_role', 'all')
                    ->orWhere('target_role', $targetRole);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
}
