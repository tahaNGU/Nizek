<?php

namespace App\Http\Requests;

use App\RestFullApi\Facade\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class SearchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $response = ApiResponse::withMessage('Validation Error')->withData($validator->errors())->withStatus(Response::HTTP_UNPROCESSABLE_ENTITY)->Builder();
        throw new HttpResponseException($response);
    }
}
