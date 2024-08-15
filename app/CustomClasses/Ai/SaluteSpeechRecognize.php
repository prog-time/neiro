<?php

namespace App\CustomClasses\Ai;

use App\Jobs\ProcessAiRecognizeGenerate;
use App\Models\GenerateAiRecognize;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SaluteSpeechRecognize extends AiSaluteSpeech implements AiTemplate
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
     * Контроллер управления
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     * @throws \Exception
     */
    public function aiController(array $requestData): array
    {
        if (empty($requestData)) {
            throw new \Exception("Передан пустой контекст в модель {$this->aiName}", 1);
        }

        return $this->asyncRecognize($requestData);
    }

    /**
     * Ассинхронное распознование речи
     *
     * @param array $dataQuery - параметры ззапроса
     * @return array|void
     */
    public function asyncRecognize(array $dataQuery)
    {
        $fileData = new File($dataQuery['file']);

        switch ($fileData->getMimeType()) {
            case "audio/mpeg":
                $typeFile = 'MP3';
                break;
            case "audio/ogg":
                $typeFile = 'OPUS';
                break;
            default:
                throw new \Exception("Данный формат не поддерживается", 500);
        }

        /* загрузка файла */
        $resultQueryUpload = $this->uploadFile($fileData, 'audio');
        if (empty($resultQueryUpload['result']['request_file_id'])) {
            throw new \Exception("Произошла ошибка при загрузке файла!");
        }

        $dataQuery = [
            'options' => [
                'model' => 'general',
                'audio_encoding' => $typeFile,
                'channels_count' => 1
            ],
            'request_file_id' => $resultQueryUpload['result']['request_file_id']
        ];

        /* отправка материала для распознования речи */
        return $this->sendQuery_asyncRecognize($dataQuery);
    }

    /**
     * Подача материала для распознования речи
     *
     * @param array $dataQuery
     * @return array
     * @throws \Exception
     */
    private function sendQuery_asyncRecognize(array $dataQuery): array
    {
        $queryHeading = $this->getBasicHeaders();
        $resultData = \ParserMethods::postQuery($this->basicUrl . "/speech:async_recognize", json_encode($dataQuery), $queryHeading);

        if ($resultData["status"] == 200) {
            return $this->createQueueJob($resultData);
        } else {
            throw new \Exception("Произошла ошибка в процессе распознования речи", 1);
        }
    }

    /**
     * Добавление задачи в очередь
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     */
    public function createQueueJob(array $dataQuery): array
    {
        $generateItem = GenerateAiRecognize::create([
            "name_service" => $this->aiName,
            "data_query" => json_encode($dataQuery),
            "status" => "processing",
        ]);

        ProcessAiRecognizeGenerate::dispatch($generateItem->id)
            ->onQueue('default')
            ->delay(now()->addSeconds(10));

        return [
            'message' => 'Задача поставлена в очередь',
            'resource_id' => $generateItem->id,
            'type_resource' => 'speech_recognition',
            'type_ai' => $this->aiName,
        ];
    }

    /**
     * Получение результата выполнения задания
     *
     * @param mixed $idResource - ID задания
     * @return array
     */
    public function getResultGenerate(int $idResource): array
    {
        try {
            $generateResource = GenerateAiRecognize::getGenerateById($idResource);
            if (empty($generateResource->data_query)) {
                throw new \Exception('Задача с таким номером не найдена!', 404);
            }

            $dataQuery = json_decode($generateResource->data_query, true);
            if (empty($dataQuery['result']['id'])) {
                throw new \Exception('Не найден ID ресурса!', 404);
            }

            $gigaChatImage = new SaluteSpeechRecognize();
            $resultQuery = $gigaChatImage->getStatusTask($dataQuery['result']['id']);
            if ($resultQuery['status'] != 200) {
                throw new \Exception('Ресурса с таким ID нет!', 404);
            }

            if ($resultQuery['result']['status'] === 'RUNNING') {
                throw new \Exception('Задача ещё выполняется!', 200);
            } else if ($resultQuery['result']['status'] === 'DONE') {
                $resultQuery = $gigaChatImage->downloadResult($resultQuery['result']['response_file_id']);
                return [
                    'status' => true,
                    'data' => [
                        'normalized_text' => $resultQuery[0]['results'][0]['normalized_text']
                    ]
                ];
            }

        } catch (\Exception $e) {
            $codeError = $e->getCode() ?? 500;
            $messageError = ($codeError !== 500) ? $e->getMessage() : 'Техническая ошибка. Мы уже знаем и исправляем!';

            return [
                'status' => false,
                'data' => [
                    'code' => $codeError,
                    'error' => $messageError
                ]
            ];
        }
    }

}
