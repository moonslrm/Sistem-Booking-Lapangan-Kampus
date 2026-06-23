<?php

namespace Tests\Unit\Services;

use App\Services\SlotLockService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SlotLockServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::store('array')->flush();
    }

    public function test_acquire_lock_succeeds_for_unlocked_slot(): void
    {
        $service = new SlotLockService();

        $locked = $service->acquireLock(1, '2026-06-23', 10, 600);

        $this->assertTrue($locked);
        $this->assertTrue(Cache::has('lock:slot:1:2026-06-23'));
        $this->assertSame('10', Cache::get('lock:slot:1:2026-06-23'));
    }

    public function test_acquire_lock_fails_when_slot_is_locked_by_another_user(): void
    {
        $service = new SlotLockService();

        $this->assertTrue($service->acquireLock(1, '2026-06-23', 10, 600));
        $this->assertFalse($service->acquireLock(1, '2026-06-23', 11, 600));
    }

    public function test_release_lock_fails_when_not_lock_owner(): void
    {
        $service = new SlotLockService();

        $this->assertTrue($service->acquireLock(1, '2026-06-23', 10, 600));
        $this->assertFalse($service->releaseLock(1, '2026-06-23', 11));
        $this->assertTrue(Cache::has('lock:slot:1:2026-06-23'));
    }

    public function test_release_lock_succeeds_for_lock_owner(): void
    {
        $service = new SlotLockService();

        $this->assertTrue($service->acquireLock(1, '2026-06-23', 10, 600));
        $this->assertTrue($service->releaseLock(1, '2026-06-23', 10));
        $this->assertFalse(Cache::has('lock:slot:1:2026-06-23'));
    }

    public function test_is_locked_returns_true_when_lock_exists(): void
    {
        $service = new SlotLockService();

        $this->assertFalse($service->isLocked(1, '2026-06-23'));

        $service->acquireLock(1, '2026-06-23', 10, 600);

        $this->assertTrue($service->isLocked(1, '2026-06-23'));
    }
}
