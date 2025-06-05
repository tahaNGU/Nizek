<?php

namespace App\Repositories\StockStream;

interface StockStreamEloquentInterface
{
    /**
     * @param array $data
     * @return void
     */
    public function create(array $data): void;

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $orderDirection
     * @return mixed
     */
    public function findWhere(array $where, string $orderBy, string $orderDirection): mixed;


    /**
     * @param string $orderBy
     * @param string $orderDirection
     * @param array $where
     * @return mixed
     */
    public function findWhereBetween(string $orderBy, string $orderDirection, array $where): mixed;

}
