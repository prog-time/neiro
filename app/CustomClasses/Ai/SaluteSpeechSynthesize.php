<?php

namespace App\CustomClasses\Ai;

use App\Jobs\ProcessAiRecognizeGenerate;
use App\Jobs\ProcessAiSynthesizeGenerate;
use App\Models\GenerateAiRecognize;
use App\Models\GenerateAiSynthesize;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

class SaluteSpeechSynthesize extends AiSaluteSpeech implements AiTemplate
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
     * @param array $requestData - параметры запроса
     * @return array
     * @throws \Exception
     */
    public function aiController(array $requestData): array
    {
        if (empty($dataQuery)) {
            throw new \Exception("Передан пустой контекст в модель {$this->aiName}", 1);
        }

        return $this->asyncSynthesize($dataQuery);
    }

    /**
     * Ассинхронное распознование речи
     *
     * @param array $dataQuery - параметры запроса
     * @return array|void
     */
    public function asyncSynthesize(array $dataQuery)
    {
        $fileData = new File($dataQuery['file']);

        /* загрузка файла */
        $resultQueryUpload = $this->uploadFile($fileData, 'text');
        if (empty($resultQueryUpload['result']['request_file_id'])) {
            throw new \Exception("Произошла ошибка при загрузке файла!");
        }

        $dataQuery = [
            'audio_encoding' => 'opus',
            'voice' => 'Ost_24000',
            'request_file_id' => $resultQueryUpload['result']['request_file_id']
        ];

        /* отправка материала для распознования речи */
        return $this->sendQuery_asyncSynthesize($dataQuery);
    }

    /**
     * Подача материала для распознования речи
     *
     * @param array $dataQuery
     * @return array
     * @throws \Exception
     */
    private function sendQuery_asyncSynthesize(array $dataQuery): array
    {
        $queryHeading = $this->getBasicHeaders();
        $resultData = \ParserMethods::postQuery($this->basicUrl . "/text:async_synthesize", json_encode($dataQuery), $queryHeading);

        if ($resultData["status"] == 200) {
            return $this->createQueueJob($resultData);
        } else {
            throw new \Exception("Произошла ошибка в процессе синтеза речи", 1);
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
        $generateItem = GenerateAiSynthesize::create([
            "name_service" => $this->aiName,
            "data_query" => json_encode($dataQuery),
            "status" => "processing",
        ]);

        ProcessAiSynthesizeGenerate::dispatch($generateItem->id)
            ->onQueue('default')
            ->delay(now()->addSeconds(10));

        return [
            'message' => 'Задача поставлена в очередь',
            'resource_id' => $generateItem->id,
            'type_resource' => 'speech_synthesizes',
            'type_ai' => $this->aiName,
        ];
    }

    /**
     * Получение результата выполнения задания
     *
     * @param int $idResource - ID задания
     * @return array
     */
    public function getResultGenerate(int $idResource): array
    {
        try {
            $generateResource = GenerateAiSynthesize::getGenerateById($idResource);
            if (empty($generateResource->data_query)) {
                throw new \Exception('Задача с таким номером не найдена!', 404);
            }

            $dataQuery = json_decode($generateResource->data_query, true);
            if (empty($dataQuery['result']['id'])) {
                throw new \Exception('Не найден ID ресурса!', 404);
            }

            $aiBot = new SaluteSpeechSynthesize();
            $resultQuery = $aiBot->getStatusTask($dataQuery['result']['id']);
            if ($resultQuery['status'] != 200) {
                throw new \Exception('Ресурса с таким ID нет!', 404);
            }

            if ($resultQuery['result']['status'] === 'RUNNING') {
                throw new \Exception('Задача ещё выполняется!', 200);
            } else if ($resultQuery['result']['status'] === 'DONE') {
                $resultQuery = $aiBot->downloadResult($resultQuery['result']['response_file_id']);

                if (!empty($resultQuery)) {
                    $fileName = "speech_synthesizes/{$generateResource->id}.opus";
                    $resultSave = Storage::disk('local')->put($fileName, $resultQuery);
                    if (!empty($resultSave)) {
                        $urlFile = env('APP_URL') . "/api/speech_synthesizes/{$generateResource->id}";

                        return [
                            'status' => true,
                            'data' => [
                                'file' => $urlFile
                            ]
                        ];
                    }
                } else {
                    return [
                        'status' => false,
                        'data' => "Неудалось синтезировать речь!"
                    ];
                }
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
