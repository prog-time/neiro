<?php

namespace App\Http\Requests;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompilationTextRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'model' => [
                'required',
                'string',
            ],
            'messages' => [
                'required',
                'max:1500'
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
