<?php

namespace App\Jobs;

use App\Services\MidtransService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private array $payload)
    {
        $this->onQueue('default');
    }

    public function handle(MidtransService $service): void
    {
        try {
            $service->handleWebhookNotification($this->payload);
        } catch (\Throwable $e) {
            Log::channel('booking')->error('ProcessPaymentWebhookJob failed.', ['error' => $e->getMessage(), 'payload' => $this->payload]);
            // don't rethrow to avoid worker crash for now
        }
    }
}
