<?php

namespace Tests\Feature;

use App\Models\GenerateAiRecognize;
use App\Models\User;
use Illuminate\Http\File;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\AiTestCase;
use Tests\TestCase;

class SaluteSpeechRecognizeTest extends AiTestCase
{
    protected string $typeAi = "SaluteSpeech";

    protected string $basicUrl = "/api/compilations/speech_recognition";

    public function __construct(string $name)
    {
        parent::__construct($name);
        $this->basicUrl = "/api/compilations/speech_recognition";
        $this->basicGetUrl = $this->basicUrl . "/get";
    }

    /**
     * Распознование речи
     *
     * @return void
     */
    public function test_speech_recognize(): void
    {
        $nameTestFile = "dlya_raspoznovaniya.mp3";
        $testFilePath = storage_path("test/{$nameTestFile}");
        $file = new File($testFilePath);

        // Данные запроса
        $requestData = [
            'model' => $this->typeAi,
            'file' => $file,
        ];

        // Выполнение POST-запроса
        $response = $this->post($this->basicUrl, $requestData);

        $this->checkCreateResource($response);
    }

    /**
     * Запрос несуществующего элемента
     *
     * @return void
     */
    public function test_get_speech_recognize_404(): void
    {
        $requestData = $this->getElementCreateParams(999);
        $response = $this->post($this->basicGetUrl, $requestData);
        $this->checkGetResource_404($response);
    }

    /**
     * Запрос элемента который в процессе создания
     *
     * @return void
     */
    public function test_get_speech_recognize_202(): void
    {
        $generateElem = GenerateAiRecognize::getGenerateLast();
        $requestData = $this->getElementRequestData($generateElem);
        if (!empty($requestData)) {
            $response = $this->post($this->basicGetUrl, $requestData);
            $this->checkGetResource_202($response);
        }
    }

    /**
     * Запрос несуществующего элемента
     *
     * @return void
     */
    public function test_get_speech_recognize_200(): void
    {
        $generateElem = GenerateAiRecognize::getGenerateNotNullResult();
        $requestData = $this->getElementRequestData($generateElem);
        if (!empty($requestData)) {
            $response = $this->post($this->basicGetUrl, $requestData);
            $this->checkGetResource_200($response);
        }
    }

}
