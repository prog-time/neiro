<?php

namespace App\CustomClasses\Ai;

use App\Jobs\ProcessAiTextGenerate;
use App\Models\GenerateAiText;

class GigaChatFull extends AiGigaChat implements AiTemplate
{
    /**
     * Название нейросети
     * @var string
     */
    public string $aiName = "GigaChat";

    /**
     * Базовый URL для отправки запросов
     * @var string
     */
    public string $basicUrl = "https://gigachat.devices.sberbank.ru/api/v1";

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
     * Подготовка параметров для запроса
     *
     * @param array $context - контекст запроса
     * @return array
     */
    public static function preparationMaterialsForRequest(mixed $context): array
    {
        return [
            "model" => "GigaChat",
            "stream" => false,
            "update_interval" => 0,
            "function_call" => "auto",
            "messages" => $context,
        ];
    }

    /**
     * Контроллер управления
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     * @throws \Exception
     */
    public function aiController(array $requestData): array
    {
        $dataQuery = \App\CustomClasses\Ai\GigaChatFull::preparationMaterialsForRequest($requestData['messages']);
        if (empty($dataQuery)) {
            throw new \Exception("Передан пустой контекст в модель {$this->aiName}", 1);
        }

        $resultData = $this->sendQuery($dataQuery);
        if (empty($resultData['choices'])) {
            throw new \Exception("Модель {$this->aiName} выдала ошибку! Обратитесь в техническую поддержку", 1);
        }

        $messageContent = $resultData['choices'][0]['message']['content'] ?? "";
        if (empty($messageContent)) {
            throw new \Exception("Модель {$this->aiName} не смогла сгенерировать изображение", 1);
        }

        /* проверяем наличие изображений */
        $resultImagesContext = $this->convertingResult($messageContent);
        $listImagesUrl = [];
        if (!empty($resultImagesContext)) {
            foreach ($resultImagesContext as $idImage) {
                $listImagesUrl[] = $this->getImageById($idImage);
            }
        }

        return [
            "messages" => $resultData['choices'],
            "images" => $listImagesUrl ?? []
        ];
    }

    /**
     * Добавление задачи в очередь
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     */
    public function createQueueJob(array $dataQuery): array
    {
        $generateItem = GenerateAiText::create([
            "name_service" => $this->aiName,
            "data_query" => json_encode($dataQuery),
            "status" => "processing",
        ]);

        ProcessAiTextGenerate::dispatch($generateItem->id)
            ->onQueue('default');

        return [
            'message' => 'Задача поставлена в очередь',
            'resource_id' => $generateItem->id,
            'type_resource' => 'image',
            'type_ai' => $this->aiName,
        ];
    }

    /**
     * @param int $idResource
     * @return array
     */
    public function getResultGenerate(int $idResource): array
    {
        return [];
    }

}
