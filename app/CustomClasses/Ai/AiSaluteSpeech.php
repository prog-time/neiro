<?php

namespace App\CustomClasses\Ai;

use App\Jobs\ProcessAiRecognizeGenerate;
use App\Models\GenerateAiRecognize;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

abstract class AiSaluteSpeech
{
    /**
     * Название нейросети
     * @var string
     */
    public string $aiName = "SaluteSpeech";

    /**
     * Базовый URL для отправки запросов
     * @var string
     */
    public string $basicUrl = "https://smartspeech.sber.ru/rest/v1";

    /**
     * Токен для авторизации
     * @var string
     */
    protected string $authToken;

    /**
     * Токен для отправки запросов
     * @var string
     */
    protected string $accessToken;

    /**
     * Ключ для авторизации
     * @var string|mixed
     */
    protected string $scope;

    /**
     * Ключ для запросов
     * @var string
     */
    protected string $rqUID;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->authToken = env("SALUTESPEECH_AUTH_TOKEN");
        $this->scope = env("SALUTESPEECH_SCOPE");
        $this->rqUID = "6f0b1291-c7f3-43c6-bb2e-9f3efb2dc98e";

        if (!$this->authorisation()) {
            throw new \Exception("Не удалось авторизоваться в нейросети {$this->aiName}!", 1);
        }
    }

    /**
     * Подготовка параметров для запроса
     *
     * @param array $context - контекст запроса
     * @return array
     */
    public static function preparationMaterialsForRequest(mixed $context): array
    {
        return [];
    }

    /**
     * Базовые заголовки для отправки запросов
     *
     * @return string[]
     */
    protected function getBasicHeaders(): array
    {
        return [
            "Authorization: Bearer {$this->accessToken}",
        ];
    }

    /**
     * Авторизация
     *
     * @return bool
     */
    protected function authorisation(): bool
    {
        try {
            $queryParams = [
                'scope' => $this->scope
            ];
            $queryHeading = [
                "Authorization: Bearer {$this->authToken}",
                "RqUID: {$this->rqUID}",
                "Content-Type: application/x-www-form-urlencoded",
            ];

            $resultQuery = \ParserMethods::postQuery("https://ngw.devices.sberbank.ru:9443/api/v2/oauth", http_build_query($queryParams), $queryHeading);
            if (empty($resultQuery['access_token'])) {
                throw new \Exception("Ошибка авторизации {$this->aiName}");
            }

            $this->accessToken = $resultQuery['access_token'];
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Загрузка файла для распознования речи
     *
     * @param File $fileData - аудио файл
     * @return mixed
     */
    protected function uploadFile(File $fileData, string $typeFile)
    {
        $fileData = $fileData->getContent();

        $headers = $this->getBasicHeaders();
        if ($typeFile === 'audio') {
            $headers[] = 'Content-Type: audio/mpeg';
        } else {
            $headers[] = 'Content-Type: text/plain';
        }

        return \ParserMethods::postQuery_binaryFile($this->basicUrl . "/data:upload", $fileData, $headers);
    }

    /**
     * Получение статуса распознования речи
     *
     * @param string $resultID
     * @return mixed|null
     */
    public function getStatusTask(string $resultID)
    {
        $queryHeading = $this->getBasicHeaders();
        $dataQuery = [
            'id' => $resultID
        ];
        return \ParserMethods::getQuery( "https://smartspeech.sber.ru/rest/v1/task:get", $dataQuery, $queryHeading);
    }

    /**
     * Получение материала по распознованию речи
     *
     * @param string $responseFileID
     * @return mixed|null
     */
    public function downloadResult(string $responseFileID)
    {
        $queryHeading = $this->getBasicHeaders();
        $dataQuery = [
            'response_file_id' => $responseFileID
        ];
        return \ParserMethods::getQuery( "https://smartspeech.sber.ru/rest/v1/data:download", $dataQuery, $queryHeading);
    }

}
