<?php

namespace App\Console\Commands\Testing;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class CheckWebhookCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:webhook';

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
         // Upload File To Discord
        //  $data = [
        //     'file_preview' => $row
        // ];
        // $response = Http::attach('files', File::get(public_path('documents/attachments/BXkZaHbeNBiu3VRWsoNWg46KqMWu2SloboyrZO3q.docx')), 'name-file-234.docx')
        /*$response = Http::attach('files[1]', File::get(public_path('documents/attachments/BXkZaHbeNBiu3VRWsoNWg46KqMWu2SloboyrZO3q.docx')), 'name-file-234.docx')
            ->post(env('DISCORD_WEBHOOK_URL_ATTACHMENT_PREVIEW'), [
                'content' => 'test',
                // 'embeds' => [
                //     'title' => 'Test',
                //     'description' => 'Test',
                // ]
                'files[1]' => [
                    [
                        'id'=> 1,
                        'filename' => 'name-file-234.docx',
                        'description' => 'Test',
                    ]
                ]
                //File::get(public_path('documents/attachments/BXkZaHbeNBiu3VRWsoNWg46KqMWu2SloboyrZO3q.docx')),
            ]);

        $response_decode = json_decode($response, true);

        dump($response_decode);*/
        // dump($response_decode["attachments"][0]["url"]);
        // Insert Data
        // $data = [
        //     'resource_preview_resource_id' => $resource_id,
        //     'resource_preview_url' => $response_decode["attachments"][0]["url"],
        // ];

        try {
            // Coba membaca file dari S3
            $test = Storage::disk('s3')->get('test-word.docx');
            //get url
            $url = Storage::disk('s3')->url('test-word.docx');
            dump( $url);
        } catch (\Exception $e) {
            dump('Gagal terhubung ke S3. Pesan kesalahan: ' . $e->getMessage());
        }
    }
}
