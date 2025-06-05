<?php

namespace App\RestFullApi;

class ApiResponse
{
    private mixed $data = [];
    private ?string $message = null;
    private int $status;

    /**
     * @param mixed $data
     * @return void
     */
    public function setData(mixed $data)
    {
        $this->data = $data;
    }

    /**
     * @param string|null $message
     * @return void
     */
    public function setMessage(?string $message)
    {
        $this->message = $message;
    }

    /**
     * @param string $status
     * @return void
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function response()
    {
        $body = [];
        !is_null($this->message) && $body['message'] = $this->message;
        !is_null($this->data) && $body['data'] = $this->data;

        return response()->json($body, $this->status);
    }
}