<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

use App\Services\Whatsapp\WhatsappWebhookService;

class WhatsappSendMessageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'whatsapp:send-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $appKey = env('WHATSAPP_BOT_APP_KEY');
        // $authKey = env('WHATSAPP_BOT_AUTH_KEY');
        // $to = '087727598653';
        // $message = 'Example message';
        // $sandbox = 'false';

        // $response = Http::post(env('WHATSAPP_BOT_API_URL') . 'create-message', [
        //     'appkey' => $appKey,
        //     'authkey' => $authKey,
        //     'to' => $to,
        //     'sandbox' => $sandbox,
        //     'template_id' => env('WHATSAPP_BOT_TEMPLATE_ID_SPDP_READY_TO_SIGNED'),
        //     'variables' => array(
        //       '{document_id}' => '123',
        //      )
        // ]);

        // $this->info($response->body());
        
        $whatsappWebhookService = new WhatsappWebhookService();

        $response = $whatsappWebhookService->sendMessageTemplate(
            destinationPhoneNumber: '6287727598653',
            templateId: env('WHATSAPP_BOT_TEMPLATE_ID_DOC_READY_TO_SIGNED'),
            props: [
                '{positionName}' => 'KASAT LANTAS RES X',
                '{documentNumber}' => 'SPDP/1/X',
                '{documentName}' => 'Surat Pemberitahuan Dimulainya Penyidikan (SPDP)',
                '{accidentNumber}' => 'LP/1/X',
            ]
        );

        dump($response);
    }
}
