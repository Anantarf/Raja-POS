<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAuditLogJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $action,
        public string $description,
        public ?int $userId = null,
        public array $context = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('single')->info("[AUDIT LOG] {$this->action}: {$this->description}", [
            'user_id' => $this->userId,
            'context' => $this->context,
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
