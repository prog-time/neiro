<?php

namespace App\Http\Requests;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SpeechSynthesizeRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'max:1024',
                'mimetypes:text/plain'
            ],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Параметр :attribute является обязательным!',
            'file' => 'В параметр :attribute необходимо передать файл!',
            'size' => 'Файл не должен превышать 1mb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponceApiController::responceData(['errors' => $validator->errors()], 422)
        );
    }
}
