<?php

namespace App\CustomClasses\Ai;

use App\Http\Requests\CompilationGetResultRequest;

interface AiTemplate
{

    /**
     * Подготовка параметров для запроса
     *
     * @param array $context - контекст запроса
     * @return array
     */
    public static function preparationMaterialsForRequest(mixed $context): array;

    /**
     * Контроллер управления
     *
     * @param array $dataQuery - параметры запроса
     * @return array
     * @throws \Exception
     */
    public function aiController(array $requestData): array;

    /**
     * Создание задачи для генерации контента
     *
     * @param array $dataQuery
     * @return array
     */
    public function createQueueJob(array $dataQuery): array;

    /**
     * Получение результата генерации контента
     *
     * @param mixed $idResource
     * @return mixed
     */
    public function getResultGenerate(int $idResource): array;
}
