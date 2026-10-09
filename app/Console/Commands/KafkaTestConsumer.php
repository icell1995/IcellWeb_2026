<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\ConsumerMessage;
use Junges\Kafka\Contracts\MessageConsumer;

class KafkaTestConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:test-consumer';


    /**
     * The console command description.
     *
     * @var string
     */
    
    protected $description = 'Test consumer Kafka local';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $this->info('Menunggu message dari Kafka...');

        $consumer = Kafka::consumer(['test-laravel'])
            ->withConsumerGroupId('laravel-test-group')
            ->withHandler(function (
                ConsumerMessage $message,
                MessageConsumer $consumer
            ) {
                $this->info('Message diterima:');

                dump($message->getBody());
            })
            ->build();

        $consumer->consume();

        return Command::SUCCESS;
    }
}
