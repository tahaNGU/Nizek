<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImportExcelRowJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function handle()
    {
        $insertData = [];

        foreach ($this->rows as $row) {
            try {
                $recordedAt = Carbon::createFromFormat('m/d/Y', $row[0])->timestamp;
            } catch (\Exception $e) {
                // تاریخ نامعتبر بود، رد می‌کنیم
                continue;
            }

            $insertData[] = [
                'recorded_at' => $recordedAt,
                'recorded_date' => $row[0],
                'stock_price' => $row[1],
            ];
        }

        if (!empty($insertData)) {
            DB::table('stock_streams')->insert($insertData);
        }
    }
}
