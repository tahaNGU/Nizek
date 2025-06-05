<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UploadExcelRequest;
use App\Jobs\ImportExcelRowJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\RestFullApi\Facade\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class StockStreamController extends Controller
{
    public function importExcel(UploadExcelRequest $request)
    {

        $collection = Excel::toCollection(null, $request->file('file'))[0];

        $dataRows = $collection->slice(1);

        $chunks = $dataRows->chunk(100);

        foreach ($chunks as $chunk) {
            $row = json_decode(json_encode($chunk), true);
            ImportExcelRowJob::dispatch($row);
        }
        return ApiResponse::withMessage('Queue Started Successfully')->withStatus(Response::HTTP_OK)->Builder();
    }

    public function report(Request $request)
    {
        $latest = DB::table('prices')
            ->orderByDesc('date')
            ->first();
        if (!$latest) {
            return response()->json(['message' => 'No data available.'], 404);
        }

        $periods = [
            '1D' => Carbon::createFromTimestamp($latest->date)->subDay(),
            '1M' => Carbon::createFromTimestamp($latest->date)->subMonth(),
            '3M' => Carbon::createFromTimestamp($latest->date)->subMonths(3),
            '6M' => Carbon::createFromTimestamp($latest->date)->subMonths(6),
            'YTD' => Carbon::createFromTimestamp($latest->date)->startOfYear(),
            '1Y' => Carbon::createFromTimestamp($latest->date)->subYear(),
            '3Y' => Carbon::createFromTimestamp($latest->date)->subYears(3),
            '5Y' => Carbon::createFromTimestamp($latest->date)->subYears(5),
            '10Y' => Carbon::createFromTimestamp($latest->date)->subYears(10),
            'MAX' => null,
        ];

        $results = [];

        foreach ($periods as $label => $startDate) {
            if ($startDate) {
                $startTimestamp = $startDate->timestamp;
                $first = DB::table('prices')
                    ->where('date', '>=', $startTimestamp)
                    ->orderBy('date')
                    ->first();

                if (!$first) {
                    $fallbackDate = Carbon::createFromTimestamp($startTimestamp)->subDays(5)->timestamp;
                    $first = DB::table('prices')
                        ->whereBetween('date', [$fallbackDate, $startTimestamp])
                        ->orderByDesc('date')
                        ->first();

                }

            }
            else {
                $first = DB::table('prices')
                    ->orderBy('date')
                    ->first();
            }

            $valueChange = $latest->stock_price - $first->stock_price;
            $percentageChange = round(($valueChange / $first->stock_price) * 100, 2);
            $results[$label] = [
                'percentage_change' => $percentageChange,
                'persian_date' => $first->date_string,
            ];
        }

        return response()->json([
            'as_of' => Carbon::createFromTimestamp($latest->date)->toDateString(),
            'report' => $results,
        ]);
    }


    public function customReport(SearchRequest $request)
    {

        $from = Carbon::parse($request->input('from'))->startOfDay()->timestamp;
        $to = Carbon::parse($request->input('to'))->endOfDay()->timestamp;
        $startPrice = DB::table('prices')
            ->where('date', '>=', $from)
            ->orderBy('date', 'asc')
            ->first();

        $endPrice = DB::table('prices')
            ->where('date', '<=', $to)
            ->orderBy('date', 'desc')
            ->first();

        if (!$startPrice || !$endPrice) {
            return response()->json([
                'message' => 'Data not available for selected range.',
            ], 404);
        }

        $percentageChange = (($endPrice->stock_price / $startPrice->stock_price)-1) * 100;

        return response()->json([
            'from' => date('Y-m-d', $startPrice->date),
            'fromNumber' => $startPrice->stock_price,
            'to' => date('Y-m-d', $endPrice->date),
            'toNumber' => $endPrice->stock_price,
            'percentage_change' => $percentageChange,
        ]);
    }



}
