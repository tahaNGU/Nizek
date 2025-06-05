<?php

namespace App\Repositories\StockStream;

use App\Models\StockStream;

class StockStreamEloquentRepository implements StockStreamEloquentInterface
{

    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void
    {
        StockStream::insert($data);
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $orderDirection
     * @return mixed
     */
    public function findWhere(array $where, string $orderBy, string $orderDirection): mixed
    {
        return StockStream::where($where)->orderBy($orderBy, $orderDirection)->first();
    }

    /**
     * @param string $orderBy
     * @param string $orderDirection
     * @param array $where
     * @return mixed
     */
    public function findWhereBetween(string $orderBy, string $orderDirection, array $where): mixed
    {
        return StockStream::whereBetween($where)
            ->orderBy($orderBy, $orderDirection)
            ->first();
    }
}
