<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class FCMService
{ 
    public static function send($token,$data,$notification)
    {
       
        $SERVER_API_KEY = 'AAAALByhJdM:APA91bHXLCrv6kKa_W_gqa1hk3aOhNffNqzBHaX1EvSpVfbCk4wzASszv074rEaVgEQxlkNDV4_Q34AuEWlkR0b1-QZvYjpb_QOH0rRyAm66U_c4QRfOItJNEbWuKpfkMMkLmWpKA8Kq';

        $token_1 = $token;

        $data = [

            "registration_ids" => [
                $token_1,
            ],
          
            'data'=>$data,
            "notification" => $notification,


        ];

        $dataString = json_encode($data);

        $headers = [

            'Authorization: key=' . $SERVER_API_KEY,

            'Content-Type: application/json',

        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');

        curl_setopt($ch, CURLOPT_POST, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

        $response = curl_exec($ch);

    }
}