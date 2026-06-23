<?php

namespace App\Jobs;

use App\Services\SlotLockService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReleaseExpiredLockJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $slotId, private string $date, private int $userId)
    {
        $this->onQueue('default');
    }

    public function handle(SlotLockService $lockService): void
    {
        $released = $lockService->releaseLock($this->slotId, $this->date, $this->userId);

        Log::channel('booking')->info('ReleaseExpiredLockJob handled.', [
            'slot_id' => $this->slotId,
            'date' => $this->date,
            'user_id' => $this->userId,
            'released' => $released,
        ]);
    }
}
