<?php

namespace App\Jobs;

use App\Models\AuditLog;
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
        public array $context = [],
        public ?int $locationId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('single')->info("[AUDIT LOG] {$this->action}: {$this->description}", [
            'user_id' => $this->userId,
            'location_id' => $this->locationId,
            'context' => $this->context,
            'timestamp' => now()->toIso8601String(),
        ]);

        try {
            AuditLog::create([
                'user_id' => $this->userId,
                'location_id' => $this->locationId,
                'action' => $this->action,
                'description' => $this->description,
                'context' => $this->context,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to create AuditLog record: '.$e->getMessage());
        }
    }
}
