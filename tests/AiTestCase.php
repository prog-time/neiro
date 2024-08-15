<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Testing\TestResponse;

abstract class AiTestCase extends BaseTestCase
{
    protected string $typeAi;

    protected string $basicUrl;

    protected string $basicGetUrl;

    /**
     * Формирование параметров для запроса
     *
     * @param int $idElem
     * @return array
     */
    protected function getElementCreateParams(int $idElem): array
    {
        return [
            'resource_id' => $idElem,
            'type_ai' => $this->typeAi,
        ];
    }

    /**
     * Получение элемента для запроса
     *
     * @param string $typeElem - тип получаемого элемента
     * @return array|null
     */
    protected function getElementRequestData(?object $generateElem): ?array
    {
        return !empty($generateElem) ? self::getElementCreateParams($generateElem->id) : null;
    }

    protected function checkCreateResource(TestResponse $response): void
    {
        // Проверка статуса ответа
        $response->assertStatus(200);

        // Проверка, что статус ответа `true`
        $response->assertJson(['status' => true]);

        // Проверка структуры ответа
        $response->assertJsonStructure([
            'status',
            'data' => [
                'message',
                'resource_id',
                'type_resource',
                'type_ai'
            ],
        ]);
    }

    protected function checkGetResource_404(TestResponse $response): void
    {
        // Проверка статуса ответа
        $response->assertStatus(404);

        // Проверка структуры ответа, игнорируя конкретные значения в `data`
        $response->assertJsonStructure([
            'status',
            'data',
        ]);

        // Проверка, что статус ответа `false`
        $response->assertJson(['status' => false]);
    }

    protected function checkGetResource_202(TestResponse $response): void
    {
        // Проверка статуса ответа
        $response->assertStatus(202);

        // Проверка структуры ответа, игнорируя конкретные значения в `data`
        $response->assertJsonStructure([
            'status',
            'data',
        ]);

        // Проверка, что статус ответа `false`
        $response->assertJson(['status' => false]);
    }

    protected function checkGetResource_200(TestResponse $response): void
    {
        // Проверка статуса ответа
        $response->assertStatus(200);

        // Проверка структуры ответа, игнорируя конкретные значения в `data`
        $response->assertJsonStructure([
            'status',
            'data',
        ]);

        // Проверка, что статус ответа `false`
        $response->assertJson(['status' => false]);
    }
}
