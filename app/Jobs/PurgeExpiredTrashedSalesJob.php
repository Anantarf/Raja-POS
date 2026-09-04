<?php

namespace App\Jobs;

use App\Services\SaleCancellationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PurgeExpiredTrashedSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(SaleCancellationService $saleCancellationService): void
    {
        $purgedCount = $saleCancellationService->apply30DayAutoDeleteRetention();

        Log::info("[AUTO RETENTION] Purged {$purgedCount} trashed sales older than 30 days.");

        if ($purgedCount > 0) {
            ProcessAuditLogJob::dispatch(
                action: 'AUTO_RETENTION_PURGE',
                description: "Auto retention 30 hari memverifikasi dan menghapus {$purgedCount} transaksi sampah yang expired."
            );
        }
    }
}
