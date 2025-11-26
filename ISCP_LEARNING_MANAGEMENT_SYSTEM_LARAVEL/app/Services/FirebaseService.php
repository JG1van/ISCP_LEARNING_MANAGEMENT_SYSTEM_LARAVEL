<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService
{
    protected Database $database;
    private static ?Database $cachedDb = null;

    public function __construct()
    {
        // CEGAH Firebase inisialisasi ulang setiap request
        if (!self::$cachedDb) {
            $factory = (new Factory)
                ->withServiceAccount(config('firebase.credentials'))
                ->withDatabaseUri(config('firebase.database_url'));

            self::$cachedDb = $factory->createDatabase();
        }

        $this->database = self::$cachedDb;
    }

    public function pushMessage($roomId, array $message)
    {
        return $this->database
            ->getReference("complaint_rooms/{$roomId}/messages/{$message['id']}")
            ->set($message);
    }
    public function getChatHistory($roomId)
    {
        $ref = $this->database->getReference("complaint_rooms/{$roomId}/messages");
        $snapshot = $ref->getValue();

        if (!$snapshot) {
            return [
                'hasChat' => false,
                'messages' => [],
            ];
        }

        // FILTER CHAT VALID
        $messages = [];
        foreach ($snapshot as $msg) {
            if (isset($msg['message']) && trim($msg['message']) !== '') {
                $messages[] = $msg;
            }
        }

        return [
            'hasChat' => count($messages) > 0,
            'messages' => $messages,
        ];
    }


    public function deleteRoom($roomId)
    {
        return $this->database
            ->getReference("complaint_rooms/{$roomId}")
            ->remove();
    }
}
