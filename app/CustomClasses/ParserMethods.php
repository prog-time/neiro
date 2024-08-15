<?php

use App\Http\Controllers\ResponceApiController;
use Illuminate\Support\Str;

class ParserMethods
{
    /**
     * Отправка POST запросов
     *
     * @param string $urlQuery - URL для запроса
     * @param array $queryParams - параметры запроса
     *
     * @return void
     */
    public static function postQuery(string $urlQuery, mixed $queryParams = [], array $queryHeading = []): mixed
    {
        try {
            $ch = curl_init($urlQuery);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryParams);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $queryHeading);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            if (empty($resultQuery)) {
                throw new Exception('Запрос вызвал ошибку');
            }

            return (Str::of($resultQuery)->isJson()) ? json_decode($resultQuery, true) : $resultQuery;
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

    /**
     * Отправка файла в бинарном виде
     *
     * @param string $urlQuery - URL для запроса
     * @param string $fileData - файл в бинарном виде
     * @param array $queryHeading - заголовки запроса
     * @return mixed
     */
    public static function postQuery_binaryFile(string $urlQuery, string $fileData = "", array $queryHeading = []): mixed
    {
        try {
            $ch = curl_init($urlQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $queryHeading);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fileData);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            if (empty($resultQuery)) {
                throw new Exception('Запрос вызвал ошибку');
            }

            return (Str::of($resultQuery)->isJson()) ? json_decode($resultQuery, true) : $resultQuery;
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

    /**
     * Отправка GET запросов
     *
     * @param string $urlQuery - URL для запроса
     * @param array $queryParams - параметры запроса
     *
     * @return void
     */
    public static function getQuery(string $urlQuery, array|string $queryParams = [], array $queryHeading = []): mixed
    {
        try {
            if (!empty($queryParams)) {
                $urlQuery = $urlQuery ."?" . http_build_query($queryParams);
            }

            $ch = curl_init($urlQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $queryHeading);
            $resultQuery = curl_exec($ch);
            curl_close($ch);

            return (Str::of($resultQuery)->isJson()) ? json_decode($resultQuery, true) : $resultQuery;
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

}
