<?php

namespace App\Libraries;

class FonnteAPI
{
    protected $token;

    public function __construct()
    {
        $this->token = env('FONNTE_API_KEY');
    }

    public function sendMessage($phone, $message)
    {
        $curl = curl_init();

        $data = [
            'target'  => $phone,
            'message' => $message,
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token,
                'Content-Type: application/x-www-form-urlencoded'
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            return ['success' => false, 'error' => $error];
        }

        return json_decode($response, true);
    }
}
