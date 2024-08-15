<?php

namespace App\Jobs;

use App\CustomClasses\Ai\SaluteSpeechSynthesize;
use App\Models\GenerateAiSynthesize;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAiSynthesizeGenerate implements ShouldQueue
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
            $saluteSpeechSynthesize = new SaluteSpeechSynthesize();
            $resultContext = $saluteSpeechSynthesize->getResultGenerate($this->idResource);

            dump(123);
            dump($resultContext);

            if ($resultContext['status'] === false) {
                if ($resultContext['data']['code'] === 200) {
                    $this->fail();
                } else {
                    $this->delete();
                }
            } else {
                GenerateAiSynthesize::updateGenerateById(
                    $this->idResource,
                    [
                        "name_service" => $saluteSpeechSynthesize->aiName,
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
