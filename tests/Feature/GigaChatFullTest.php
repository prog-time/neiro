<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Tests\TestCase;

class GigaChatFullTest extends TestCase
{

    /**
     * Генерация текста (GigaChat)
     *
     * @return void
     */
    public function test_text_generate(): void
    {
        // Исходный запрос
        $requestData = [
            "model" => "GigaChat",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Сколько будет 5+3"
                ]
            ]
        ];

        // Выполнение POST-запроса
        $response = $this->postJson('/api/compilations/gigachat', $requestData);

        // Проверка статуса ответа
        $response->assertStatus(200);

        // Проверка, что статус ответа `true`
        $response->assertJson(['status' => true]);

        // Дополнительная проверка содержания первого сообщения
        $response->assertJsonPath('data.messages.0.message.role', 'assistant');

        // Проверка, что ключ `content` не пустой
        $response->assertJsonPath('data.messages.0.message.content', function ($content) {
            return !empty($content);
        });
    }

    /**
     * Генерация текста с изображением (GigaChat)
     *
     * @return void
     */
    public function test_text_and_image(): void
    {
        // Исходный запрос
        $requestData = [
            "model" => "GigaChat",
            "messages" => [
                [
                    "role" => "user",
                    "content" => "Сгенерируй изображение собаки"
                ]
            ]
        ];

        // Выполнение POST-запроса
        $response = $this->postJson('/api/compilations/gigachat', $requestData);

        // Проверка статуса ответа
        $response->assertStatus(200);

        // Проверка, что статус ответа `true`
        $response->assertJson(['status' => true]);

        // Дополнительная проверка содержания первого сообщения
        $response->assertJsonPath('data.messages.0.message.role', 'assistant');

        $response->assertJsonPath('data.messages.0.message.content', function ($content) {
            return strpos($content, '<img src="') !== false;
        });

        // Проверка, что URL изображений корректный
        $response->assertJsonPath('data.images.0', function ($imageUrl) {
            // Выполнение GET-запроса
            $response = $this->get($imageUrl);

            // Проверка статуса ответа
            $response->assertStatus(200);

            // Проверка заголовка Content-Type на соответствие формату JPG
            $response->assertHeader('Content-Type', 'image/jpeg');

            return strpos($imageUrl, 'https://neiro-api.iliya-code.ru/api/images/') === 0;
        });
    }

}
