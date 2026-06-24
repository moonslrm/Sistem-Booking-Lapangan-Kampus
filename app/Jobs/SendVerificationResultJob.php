<?php

namespace App\Jobs;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendVerificationResultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private int $userId, private bool $approved, private ?string $reason = null)
    {
        $this->onQueue('default');
    }

    public function handle(NotificationService $notificationService): void
    {
        $user = User::query()->find($this->userId);

        if (! $user) {
            return;
        }

        $notificationService->notifyVerificationResult($user, $this->approved, $this->reason);
    }

    public function failed(\Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::channel('notification')->error(
            'SendVerificationResultJob failed',
            ['user_id' => $this->userId, 'error' => $exception->getMessage()]
        );
    }
}
