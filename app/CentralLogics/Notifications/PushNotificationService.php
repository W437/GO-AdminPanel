<?php

namespace App\CentralLogics\Notifications;

use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Http;

class PushNotificationService
{
    public static function sendNotificationToHttp(array|null $data)
    {
        $config = Helpers::get_business_settings('push_notification_service_file_content');
        $key = (array)$config;
        if (data_get($key, 'project_id')) {
            $url = 'https://fcm.googleapis.com/v1/projects/' . $key['project_id'] . '/messages:send';
            $headers = [
                'Authorization' => 'Bearer ' . self::getAccessToken($key),
                'Content-Type' => 'application/json',
            ];
            try {
                Http::withHeaders($headers)->post($url, $data);
            } catch (\Exception $exception) {
                info($exception->getMessage());
                return false;
            }
        }
        return false;
    }

    public static function getAccessToken($key)
    {
        $jwtToken = [
            'iss' => $key['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => time() + 3600,
            'iat' => time(),
        ];
        $jwtHeader = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $jwtPayload = base64_encode(json_encode($jwtToken));
        $unsignedJwt = $jwtHeader . '.' . $jwtPayload;
        openssl_sign($unsignedJwt, $signature, $key['private_key'], OPENSSL_ALGO_SHA256);
        $jwt = $unsignedJwt . '.' . base64_encode($signature);

        $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]);
        return $response->json('access_token');
    }

    public static function send_push_notif_to_device($fcm_token, $data, $web_push_link = null)
    {
        $conversation_id = data_get($data, 'conversation_id', '');
        $advertisement_id = data_get($data, 'advertisement_id', '');
        $data_id = data_get($data, 'data_id', '');
        $sender_type = data_get($data, 'sender_type', '');
        $topic = data_get($data, 'topic', '');
        $order_type = data_get($data, 'order_type', '');
        $add_id = data_get($data, 'add_id', '');

        $postData = [
            'message' => [
                'token' => $fcm_token,
                'data' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'image' => (string)$data['image'],
                    'notification_type' => (string)data_get($data, 'type', ''),
                    'order_id' => (string)data_get($data, 'order_id', ''),
                    'order_type' => (string)$order_type,
                    'is_read' => '0',
                    'conversation_id' => (string)$conversation_id,
                    'advertisement_id' => (string)$advertisement_id,
                    'data_id' => (string)$data_id,
                    'sender_type' => (string)$sender_type,
                    'topic' => (string)$topic,
                    'add_id' => (string)$add_id,
                    'click_action' => $web_push_link ? (string)$web_push_link : '',
                    'sound' => 'notification.wav',
                ],
                'notification' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'image' => (string)$data['image'],
                ],
                'android' => [
                    'notification' => [
                        'channelId' => 'go',
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'notification.wav'
                        ]
                    ]
                ]
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function send_push_notif_to_topic($data, $topic, $type, $web_push_link = null)
    {
        $order_type = data_get($data, 'order_type', '');

        $payloadData = [
            'title' => (string)$data['title'],
            'body' => (string)$data['description'],
            'order_id' => (string)data_get($data, 'order_id', ''),
            'order_type' => (string)$order_type,
            'type' => (string)$type,
            'image' => (string)$data['image'],
            'title_loc_key' => (string)data_get($data, 'order_id', ''),
            'body_loc_key' => (string)$type,
            'click_action' => $web_push_link ? (string)$web_push_link : '',
            'sound' => 'notification.wav',
        ];

        $postData = [
            'message' => [
                'topic' => $topic,
                'data' => $payloadData,
                'notification' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'image' => (string)$data['image'],
                ],
                'android' => [
                    'notification' => [
                        'channelId' => 'go',
                    ]
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'notification.wav'
                        ]
                    ]
                ]
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }

    public static function send_push_notif_for_maintenance_mode($data, $topic, $type)
    {
        $postData = [
            'message' => [
                'topic' => $topic,
                'data' => [
                    'title' => (string)$data['title'],
                    'body' => (string)$data['description'],
                    'type' => (string)$type,
                    'image' => (string)$data['image'],
                    'body_loc_key' => (string)$type,
                ]
            ]
        ];

        return self::sendNotificationToHttp($postData);
    }
}
