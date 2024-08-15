<?php

namespace App\Jobs;

use App\CustomClasses\Ai\SaluteSpeechRecognize;
use App\Models\GenerateAiRecognize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiRecognizeGenerate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var mixed|string
     */
    private int $idResource;

    /**
     * Create a new job instance.
     */
    public function __construct(int $idResource = 0)
    {
        $this->idResource = $idResource;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            $saluteSpeechRecognize = new SaluteSpeechRecognize();
            $resultContext = $saluteSpeechRecognize->getResultGenerate($this->idResource);

            dump(321);
            dump($resultContext);

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
                        "name_service" => $saluteSpeechRecognize->aiName,
                        "data_result" => json_encode($resultContext['data']),
                        "status" => "success",
                    ]
                );

                return true;
            }
        } catch (\Exception $e) {
            dump($e->getMessage());
            dump($e->getLine());
        }
    }
}
