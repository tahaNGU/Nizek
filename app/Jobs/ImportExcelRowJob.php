<?php

namespace App\Jobs;

use App\Service\StockStreamService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportExcelRowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function handle(StockStreamService $stockStreamService)
    {
        $stockStreamService->makeInsertData(
            rows: $this->rows
        );
    }

}
