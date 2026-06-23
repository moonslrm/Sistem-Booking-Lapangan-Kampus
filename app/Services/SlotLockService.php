<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SlotLockService
{
    /**
     * NOTE: For CI/CD integration tests, add a Redis service to workflow so
     * distributed lock behavior can be validated against a real Redis backend.
     * For local PHPUnit runs, array cache works fine and does not require Redis.
     */
    public function acquireLock(int $slotId, string $date, int $userId, int $ttlSeconds = 600): bool
    {
        $key = $this->lockKey($slotId, $date);
        $value = (string) $userId;

        $acquired = Cache::add($key, $value, $ttlSeconds);

        if ($acquired) {
            Log::channel('booking')->info('Slot lock acquired.', [
                'slot_id' => $slotId,
                'date' => $date,
                'user_id' => $userId,
                'ttl_seconds' => $ttlSeconds,
                'key' => $key,
            ]);
            return true;
        }

        Log::channel('booking')->warning('Slot lock acquisition failed; slot currently locked by another user.', [
            'slot_id' => $slotId,
            'date' => $date,
            'user_id' => $userId,
            'key' => $key,
        ]);

        return false;
    }

    public function releaseLock(int $slotId, string $date, int $userId): bool
    {
        $key = $this->lockKey($slotId, $date);
        $currentOwner = Cache::get($key);

        if ((string) $currentOwner !== (string) $userId) {
            Log::channel('booking')->warning('Slot lock release denied; user is not lock owner.', [
                'slot_id' => $slotId,
                'date' => $date,
                'user_id' => $userId,
                'current_owner' => $currentOwner,
                'key' => $key,
            ]);
            return false;
        }

        Cache::forget($key);

        Log::channel('booking')->info('Slot lock released.', [
            'slot_id' => $slotId,
            'date' => $date,
            'user_id' => $userId,
            'key' => $key,
        ]);

        return true;
    }

    public function isLocked(int $slotId, string $date): bool
    {
        $key = $this->lockKey($slotId, $date);
        $locked = Cache::has($key);

        Log::channel('booking')->debug('Slot lock status checked.', [
            'slot_id' => $slotId,
            'date' => $date,
            'locked' => $locked,
            'key' => $key,
        ]);

        return $locked;
    }

    public function extendLock(int $slotId, string $date, int $userId, int $additionalSeconds): bool
    {
        $key = $this->lockKey($slotId, $date);
        $currentOwner = Cache::get($key);

        if ((string) $currentOwner !== (string) $userId) {
            Log::channel('booking')->warning('Slot lock extend denied; user is not lock owner.', [
                'slot_id' => $slotId,
                'date' => $date,
                'user_id' => $userId,
                'current_owner' => $currentOwner,
                'key' => $key,
            ]);
            return false;
        }

        Cache::put($key, (string) $userId, $additionalSeconds);

        Log::channel('booking')->info('Slot lock extended.', [
            'slot_id' => $slotId,
            'date' => $date,
            'user_id' => $userId,
            'additional_seconds' => $additionalSeconds,
            'key' => $key,
        ]);

        return true;
    }

    private function lockKey(int $slotId, string $date): string
    {
        return sprintf('lock:slot:%d:%s', $slotId, $date);
    }
}
