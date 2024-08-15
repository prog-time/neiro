<?php

use App\Http\Controllers\CompilationsController;
use App\Http\Controllers\ResponceApiController;
use Illuminate\Support\Facades\Route;

Route::controller(CompilationsController::class)->group(function () {
    Route::post('/compilations/gigachat', 'gigachat_full');

    Route::post('/compilations/speech_recognition', 'speech_recognition');
    Route::post('/compilations/speech_recognition/get', 'getRecognizeGenerate');

    Route::post('/compilations/speech_synthesize', 'speech_synthesize');
    Route::post('/compilations/speech_synthesize/get', 'getSynthesizeGenerate');

    Route::post('/compilations/text', 'text');

    Route::post('/compilations/image', 'image');
    Route::post('/compilations/image/get', 'getImageGenerate');
});

Route::get('/images/{id}', function (string $id) {
    $path = storage_path() . "/app/images/{$id}.jpg";
    if (File::exists($path)) {
        return Response::download($path);
    } else {
        return ResponceApiController::responceData(
            "Файл с таким ID не найден!",
            422
        );
    }
});

Route::get('/speech_synthesizes/{id}', function (string $id) {
    $path = storage_path() . "/app/speech_synthesizes/{$id}.opus";
    if (File::exists($path)) {
        return Response::download($path);
    } else {
        return ResponceApiController::responceData(
            "Файл с таким ID не найден!",
            422
        );
    }
});
