<?php

namespace App\Action;

use App\Http\Controllers\ResponceApiController;
use Illuminate\Http\JsonResponse;

class GetResourceGenerateAction
{
    public function handle(object $generateElem = null): JsonResponse
    {
        try {
            if (empty($generateElem)) {
                return ResponceApiController::responceData('Элемента с данными параметрами нет!', 404);
            }

            if ($generateElem->status === "success") {
                if (!empty($generateElem->data_result)) {
                    $dataResult = json_decode($generateElem->data_result, true);
                    return ResponceApiController::responceData($dataResult, 200);
                }
            }

            return ResponceApiController::responceData('Материал находится в очереди на генерацию', 202);

        } catch (\Exception $e) {
            return ResponceApiController::responceException($e);
        }
    }
}
