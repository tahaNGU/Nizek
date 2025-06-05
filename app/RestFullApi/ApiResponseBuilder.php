<?php

namespace App\RestFullApi;

class ApiResponseBuilder
{
    protected ApiResponse $ApiResponse;

    public function __construct()
    {
        $this->ApiResponse = new ApiResponse();
    }

    /**
     * @param string $message
     * @return $this
     */
    public function withMessage(string $message)
    {
        $this->ApiResponse->setMessage($message);
        return $this;
    }

    /**
     * @param mixed $data
     * @return $this
     */
    public function withData(mixed $data)
    {
        $this->ApiResponse->setData($data);
        return $this;
    }

    /**
     * @param int $status
     * @return $this
     */
    public function withStatus(int $status)
    {
        $this->ApiResponse->setStatus($status);
        return $this;
    }

    /**
     * @return mixed
     */
    public function Builder()
    {
        return $this->ApiResponse->response();
    }
}