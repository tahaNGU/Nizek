<?php

namespace App\RestFullApi;

class ApiResponse
{
    private mixed $data=[];
    private ?string $message=null;
    private int $status;

    public function setData(mixed $data)
    {
        $this->data=$data;
    }

    public function setMessage(?string $message)
    {
        $this->message=$message;
    }

    public function setStatus(string $status)
    {
        $this->status=$status;
    }

    public function response(){
        $body=[];

        !is_null($this->data)&&$body['data']=$this->data;
        !is_null($this->message)&&$body['message']=$this->message;

        return response()->json($body,$this->status);
    }
}