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

            $price = $row[1];

            $insertData[] = [
                'date' => Carbon::createFromFormat('m/d/Y', $row[0])->timestamp,
                'date_string' => $row[0],
                'stock_price' => $price,
            ];
        }

        DB::table('prices')->insert($insertData);
    }
}
