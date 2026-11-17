<?php

namespace App\Services\Kafka;

use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Message\Message;
use Illuminate\Support\Facades\Log;
use Exception;

class KafkaService
{
    /**
     * Kirim data ke topik Kafka.
     *
     * @param string $topic
     * @param mixed $data (array, object, atau string)
     * @param array $headers (opsional)
     * @return bool
     * @throws Exception
     */
    public function send(string $topic, mixed $data, array $headers = []): bool
    {
        try {
            // Jika data berupa array / objek, pastikan format body sesuai kebutuhan
            $body = is_array($data) || is_object($data) ? $data : $data;

            $message = new Message(
                headers: $headers,
                body: $body
            );

            $producer = Kafka::publish(config('kafka.brokers'))
                ->onTopic($topic)
                ->withSasl(
                    username: config('kafka.sasl.username'),
                    password: config('kafka.sasl.password'),
                    mechanisms: config('kafka.sasl.mechanisms'),
                    securityProtocol: config('kafka.securityProtocol')
                )
                ->withMessage($message);

            $producer->send();

            Log::info("Kafka produce success to topic [{$topic}]", [
                'topic' => $topic,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error("Kafka produce error on topic [{$topic}]: " . $e->getMessage(), [
                'topic' => $topic,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Shortcut untuk kirim data ke topik SPPTI ('icell-sppti-kirim-dev' secara default).
     *
     * @param mixed $data
     * @param array $headers
     * @return bool
     * @throws Exception
     */
    public function sendSppti(mixed $data, array $headers = []): bool
    {
        $topic = config('kafka.topics.sppti_kirim', 'icell-sppti-kirim-dev');
        return $this->send($topic, $data, $headers);
    }
}
