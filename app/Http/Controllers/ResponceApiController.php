<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ResponceApiController extends Controller
{

    /**
     * Генерация ответа для API
     *
     * @param mixed $dataResponce - параметры ответа
     * @param int $codeResponce - код ответа
     * @return JsonResponse
     */
    public static function responceData(mixed $dataResponce, int $codeResponce): JsonResponse
    {
        return response()->json(
            data: self::templateResponceData($dataResponce, $codeResponce),
            status: $codeResponce,
        );
    }

    /**
     * Формирование сообщения ответа для API
     *
     * @param mixed $dataResponce - параметры ответа
     * @param int $codeResponce - код ответа
     * @return array
     */
    public static function templateResponceData(mixed $dataResponce, int $codeResponce): array
    {
        $errorCode = [
            202,
            401,
            404,
            422,
            500,
        ];

        return [
            'status' => in_array($codeResponce, $errorCode) ? false : true,
            'data' => $dataResponce
        ];
    }

    /**
     * Ответ при ошибках
     *
     * @param \Exception $exception
     * @return JsonResponse
     */
    public static function responceException(\Exception $exception): JsonResponse
    {
        $errorCode = $exception->getCode();
        $errorData = (Str::of($exception->getMessage())->isJson()) ? json_decode($exception->getMessage(), true) : $exception->getMessage();

        if (!is_numeric($exception->getCode())) {
            $errorCode = 500;
            $errorData = "Ошибка! Обратитесь пожалуйста в техническую поддержку";
        }

        return ResponceApiController::responceData($errorData, $errorCode);
    }

}
