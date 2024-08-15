<?php

namespace App\CustomClasses\Ai;

use App\Jobs\ProcessAiImageGenerate;
use App\Models\GenerateAiImage;
use Illuminate\Support\Facades\Storage;

abstract class AiGigaChat
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

    protected string $rqUID;

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->authToken = env("GIGACHAT_AUTH_TOKEN");
        $this->scope = env("GIGACHAT_SCOPE");
        $this->rqUID = env("SBER_RQUID");

        if (!$this->authorisation()) {
            throw new \Exception("Не удалось авторизоваться в нейросети {$this->aiName}!", 1);
        }
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
            "Content-Type: application/x-www-form-urlencoded"
        ];
    }

    /**
     * Отправка запроса
     *
     * @param array $dataQuery - параметры запроса
     * @return mixed|null
     * @throws \Exception
     */
    protected function sendQuery(array $dataQuery)
    {
        if (!empty($dataQuery)) {
            return \ParserMethods::postQuery($this->basicUrl . "/chat/completions", json_encode($dataQuery), $this->getBasicHeaders());
        } else {
            throw new \Exception("Передан пустой контекст в модель {$this->aiName}", 1);
        }
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
                "Content-Type: application/x-www-form-urlencoded",
                "RqUID: {$this->rqUID}"
            ];

            $resultQuery = \ParserMethods::postQuery("https://ngw.devices.sberbank.ru:9443/api/v2/oauth", http_build_query($queryParams), $queryHeading);
            if (!empty($resultQuery['access_token'])) {
                $this->accessToken = $resultQuery['access_token'];
                return true;
            } else {
                throw new \Exception("Ошибка авторизации {$this->aiName}");
            }
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Добавление задачи в очередь
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     */
    public function createQueueJob(array $dataQuery)
    {
        $generateItem = GenerateAiImage::create([
            "name_service" => $this->aiName,
            "data_query" => json_encode($dataQuery),
            "status" => "processing",
        ]);

        ProcessAiImageGenerate::dispatch($generateItem->id)
            ->onQueue('default');

        return [
            'message' => 'Задача поставлена в очередь',
            'resource_id' => $generateItem->id,
            'type_resource' => 'image',
            'type_ai' => $this->aiName,
        ];
    }

    /**
     * Получение изображения по ID
     *
     * @param string $idImage - ID изображения в системе
     * @return string
     * @throws \Exception
     */
    protected function getImageById(string $idImage): string
    {
        $urlImages = "";
        $fileData = $this->sendQuery_getImageById($idImage);
        $fileName = "images/" . $idImage . '.jpg';

        $resultSave = Storage::disk('local')->put($fileName, $fileData);
        if (!empty($resultSave)) {
            $urlImages = env('APP_URL') . "/api/images/{$idImage}";
        }

        return $urlImages;
    }

    /**
     * Получение изображения по ID
     *
     * @param string $idImage - ID изображения
     * @return mixed|null
     * @throws \Exception
     */
    protected function sendQuery_getImageById(string $idImage)
    {
        if (empty($idImage)) {
            throw new \Exception("Передан пустой контекст в модель {$this->aiName}", 1);
        }

        $imgUrl = "https://gigachat.devices.sberbank.ru/api/v1/files/{$idImage}/content";
        return \ParserMethods::getQuery($imgUrl, [], $this->getBasicHeaders());
    }

    /**
     * Из полученного результата забираем данные о картинке
     *
     * @param $dataContext
     * @return array
     */
    protected function convertingResult(string $dataContext): array
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($dataContext);

        $tags = $doc->getElementsByTagName('img');

        $listImageCode = [];
        foreach ($tags as $tag) {
            $listImageCode[] = $tag->getAttribute('src');
        }

        return $listImageCode;
    }

}
