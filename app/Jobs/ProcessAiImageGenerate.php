<?php

namespace App\Jobs;

use App\CustomClasses\Ai\GigaChatImage;
use App\Models\GenerateAiImage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class ProcessAiImageGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Количество попыток выполнения задания.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Indicate if the job should be marked as failed on timeout.
     *
     * @var bool
     */
    public $failOnTimeout = true;
    /**
     * @var mixed|string
     */
    private mixed $idResource;
    /**
     * @var mixed|string
     */
    private mixed $typeResource;

    /**
     * Create a new job instance.
     */
    public function __construct($idResource = "")
    {
        $this->idResource = $idResource;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {


            if ($resultContext['status'] === false) {
                if ($resultContext['data']['code'] === 200) {
                    $this->fail();
                } else {
                    $this->delete();
                }
            } else {
                GenerateAiRecognize::updateGenerateById(
                    $this->idResource,
                    [
                        "name_service" => $gigaChatImage->aiName,
                        "data_result" => json_encode($resultContext),
                        "status" => "success",
                    ]
                );
            }




        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($e->getLine());

            $this->fail();
        }

    }
}
