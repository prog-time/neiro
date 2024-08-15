<?php

namespace App\Http\Requests;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CompilationImageRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'model' => [
                'required',
                'string',
            ],
            'promt_settings' => [
                'string',
                'nullable',
            ],
            'text' => [
                'required',
                'string',
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






