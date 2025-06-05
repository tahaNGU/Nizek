<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UploadExcelRequest;
use App\Service\StockStreamService;
use App\RestFullApi\Facade\ApiResponse;
use Symfony\Component\HttpFoundation\Response;

class StockStreamController extends Controller
{
    public function __construct(
        private StockStreamService $stockStreamService
    )
    {
    }

    /**
     * @param UploadExcelRequest $request
     * @return mixed
     */
    public function importExcel(UploadExcelRequest $request)
    {
        $data = $request->validated();
        $this->stockStreamService->importExcelData(
            data: $data
        );
        return ApiResponse::withMessage('Queue Started Successfully')->withStatus(Response::HTTP_OK)->Builder();
    }

    /**
     * @return mixed
     */
    public function report()
    {
        $results = $this->stockStreamService->report();
        if (!$results)
            return ApiResponse::withMessage('No data available')->withStatus(Response::HTTP_NOT_FOUND)->Builder();

        return ApiResponse::withMessage('Periods Report')->withData($results)->withStatus(Response::HTTP_OK)->Builder();

    }


    /**
     * @param SearchRequest $request
     * @return mixed
     */
    public function customReport(SearchRequest $request)
    {

        $results = $this->stockStreamService->customReport(
            data: $request->validated()
        );
        if (!$results) {
            return ApiResponse::withMessage('No data available')->withStatus(Response::HTTP_NOT_FOUND)->Builder();
        }
        return ApiResponse::withMessage('Periods Report')->withData($results)->withStatus(Response::HTTP_OK)->Builder();
    }


}
