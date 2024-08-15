<?php

namespace App\Http\Controllers;

use App\Action\GetResourceGenerateAction;
use App\Http\Requests\CompilationGetResultRequest;
use App\Http\Requests\CompilationTextRequest;
use App\Http\Requests\SpeechRecognitionRequest;
use App\Http\Requests\SpeechSynthesizeRequest;
use App\Models\GenerateAiRecognize;
use App\Models\GenerateAiSynthesize;
use Illuminate\Http\JsonResponse;

class CompilationsController extends Controller
{
    /**
     * Работа с нейросетью GigaChat
     *
     * @param CompilationTextRequest $requestParams
     * @return JsonResponse
     */
    public function gigachat_full(CompilationTextRequest $requestParams): JsonResponse
    {
        try {
            $requestData = $requestParams->all();
            switch ($requestData['model']) {
                case 'GigaChat':
                    $result = (new \App\CustomClasses\Ai\GigaChatFull())->aiController($requestData);
                    break;

                default:
                    throw new \Exception("Модель {$requestData['model']} не поддерживается!", 1);
            }

            return ResponceApiController::responceData($result, 200);
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

    /**
     * Получение результата генерации изображения
     *
     * @param CompilationGetResultRequest $requestParams
     * @return JsonResponse
     */
    public function getImageGenerate(CompilationGetResultRequest $requestParams): JsonResponse
    {
        $requestData = $requestParams->all();
        $generateElem = GenerateAiSynthesize::getGenerateByIdAndService((int)$requestData['resource_id'], $requestData['type_ai']);
        return (new GetResourceGenerateAction())->handle($generateElem);
    }

    /**
     * Распознование речи
     *
     * @param CompilationTextRequest $requestParams
     * @return JsonResponse
     */
    public function speech_recognition(SpeechRecognitionRequest $requestParams): JsonResponse
    {
        try {
            $requestData = $requestParams->all();
            switch ($requestData['model']) {
                case 'SaluteSpeech':
                    $result = (new \App\CustomClasses\Ai\SaluteSpeechRecognize())->aiController($requestData);
                    break;

                default:
                    throw new \Exception("Модель {$requestData['model']} не поддерживается!", 1);
            }

            return ResponceApiController::responceData($result, 200);
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

    /**
     * Получение результата распознования речи
     *
     * @param CompilationGetResultRequest $requestParams
     * @return JsonResponse
     */
    public function getRecognizeGenerate(CompilationGetResultRequest $requestParams): JsonResponse
    {
        $requestData = $requestParams->all();
        $generateElem = GenerateAiRecognize::getGenerateByIdAndService((int)$requestData['resource_id'], $requestData['type_ai']);
        return (new GetResourceGenerateAction())->handle($generateElem);
    }

    /**
     * Распознование речи
     *
     * @param CompilationTextRequest $requestParams
     * @return JsonResponse
     */
    public function speech_synthesize(SpeechSynthesizeRequest $requestParams): JsonResponse
    {
        try {
            $requestData = $requestParams->all();
            switch ($requestData['model']) {
                case 'SaluteSpeech':
                    $result = (new \App\CustomClasses\Ai\SaluteSpeechSynthesize())->aiController($requestData);
                    break;

                default:
                    throw new \Exception("Модель {$requestData['model']} не поддерживается!", 1);
            }

            return ResponceApiController::responceData($result, 200);
        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }

    /**
     * Получение результата синтеза речи
     *
     * @param CompilationGetResultRequest $requestParams
     * @return JsonResponse
     */
    public function getSynthesizeGenerate(CompilationGetResultRequest $requestParams): JsonResponse
    {
        $requestData = $requestParams->all();
        $generateElem = GenerateAiSynthesize::getGenerateByIdAndService((int)$requestData['resource_id'], $requestData['type_ai']);
        return (new GetResourceGenerateAction())->handle($generateElem);
    }

}
