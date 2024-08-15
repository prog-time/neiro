<?php

namespace App\Http\Requests;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SpeechRecognitionRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimetypes:audio/mpeg,audio/mp3',
                'max:10240'
            ],
        ];
    }

    public function messages()
    {
        return [
            'required' => 'Параметр :attribute является обязательным!',
            'file' => 'В параметр :attribute необходимо передать файл!',
            'file.mimetypes' => 'В параметр :attribute необходимо передать файл аудио формата!',
            'size' => 'Файл не должен превышать 10mb',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ResponceApiController::responceData(['errors' => $validator->errors()], 422)
        );
    }
}
