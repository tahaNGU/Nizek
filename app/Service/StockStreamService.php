<?php

namespace App\Service;

use App\Jobs\ImportExcelRowJob;
use App\Repositories\StockStream\StockStreamEloquentInterface;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class StockStreamService
{

    public function __construct(
        private StockStreamEloquentInterface $stockStreamEloquent
    )
    {
    }


    /**
     * @param array $data
     * @return void
     */
    public function importExcelData(array $data)
    {
        $collection = Excel::toCollection(null, $data["file"])[0];
        $dataRows = $collection->slice(1);
        $chunks = $dataRows->chunk(100);
        foreach ($chunks as $chunk) {
            $rows = json_decode(json_encode($chunk), true);
            ImportExcelRowJob::dispatch($rows);

        }
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $orderDirection
     * @return mixed
     */
    public function findWhere(array $where = [], string $orderBy = "id", string $orderDirection = 'DESC'): mixed
    {
        return $this->stockStreamEloquent->findWhere(
            where: $where,
            orderBy: $orderBy,
            orderDirection: $orderDirection
        );
    }

    /**
     * @return bool|array
     */
    public function report(): bool|array
    {
        // Get the most recent record by 'recorded_at'
        $latest = $this->findWhere(
            orderBy: 'recorded_at',
            orderDirection: 'desc'
        );
        if (!$latest) {
            return false;
        }
        // Define relative periods for report
        $date = $latest['recorded_at'];
        $periods = [
            '1D' => Carbon::createFromTimestamp($date)->subDay(),
            '1M' => Carbon::createFromTimestamp($date)->subMonth(),
            '3M' => Carbon::createFromTimestamp($date)->subMonths(3),
            '6M' => Carbon::createFromTimestamp($date)->subMonths(6),
            'YTD' => Carbon::createFromTimestamp($date)->startOfYear(),
            '1Y' => Carbon::createFromTimestamp($date)->subYear(),
            '3Y' => Carbon::createFromTimestamp($date)->subYears(3),
            '5Y' => Carbon::createFromTimestamp($date)->subYears(5),
            '10Y' => Carbon::createFromTimestamp($date)->subYears(10),
            'MAX' => null,
        ];
        $results = [];
        foreach ($periods as $label => $startDate) {
            if ($startDate) {
                $startTimestamp = $startDate->timestamp;
                // Get the first record from this period

                $first = self::findWhere(
                    where: [['recorded_at', '>=', $startTimestamp]],
                    orderBy: 'recorded_at',
                    orderDirection: 'ASC',
                );

                // Fallback: if not found, look up to 5 days earlier
                if (!$first) {
                    $fallbackDate = Carbon::createFromTimestamp($startTimestamp)->subDays(5)->timestamp;
                    $first = $this->stockStreamEloquent->findWhereBetween(
                        orderBy: 'recorded_at',
                        orderDirection: 'DESC',
                        where: ['recorded_at', [$fallbackDate, $startTimestamp]]
                    );
                }

            } else {
                // 'MAX' case: get the earliest record
                $first = self::findWhere(
                    orderBy: 'recorded_at',
                    orderDirection: 'ASC',
                );
            }

            // Calculate change in value and percentage
            $percentageChange = round((($latest->stock_price / $first->stock_price) - 1) * 100, 2);

            $results[$label] = [
                'percentage_change' => $percentageChange,
            ];
        }

        return $results;

    }

    /**
     * @param array $data
     * @return array|bool
     */
    public function customReport(array $data): array|bool
    {
        $from = Carbon::parse($data['from'])->startOfDay()->timestamp;
        $to = Carbon::parse($data['to'])->endOfDay()->timestamp;

        $startPrice = $this->findWhere(
            where: [['recorded_at', '>=', $from]],
            orderBy: 'recorded_at',
            orderDirection: 'ASC'
        );
        $endPrice = $this->findWhere(
            where: [['recorded_at', '<=', $to]],
            orderBy: 'recorded_at',
            orderDirection: 'DESC'
        );
        if (!$startPrice || !$endPrice) {
            return false;
        }
        $percentageChange = (($endPrice->stock_price / $startPrice->stock_price) - 1) * 100;
        return [
            'percentage_change' => $percentageChange,
            'from' => date('Y-m-d', $startPrice->date),
            'fromNumber' => $startPrice->stock_price,
            'to' => date('Y-m-d', $endPrice->date),
            'toNumber' => $endPrice->stock_price,
        ];
    }


    /**
     * @param array $rows
     * @return void
     */
    public function makeInsertData(array $rows)
    {
        $insertData = [];
        foreach ($rows as $row) {
            $recordedAt = Carbon::createFromFormat('m/d/Y', $row[0])->timestamp;
            $insertData[] = [
                'recorded_at' => $recordedAt,
                'recorded_date' => $row[0],
                'stock_price' => $row[1],
            ];
        }
        $this->stockStreamEloquent->create($insertData);
    }
}
