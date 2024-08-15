<?php

namespace App\Http\Requests;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompilationGetResultRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'resource_id' => [
                'required',
                'numeric'
            ],
            'type_ai' => [
                'required',
                'string',
            ],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Параметр :attribute является обязательным!',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponceApiController::responceData(['errors' => $validator->errors()], 422)
        );
    }

}






