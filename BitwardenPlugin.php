<?php

class BitwardenPlugin
{
    private static $apiUrl = 'https://api.bitwarden.com/';
    private static $apiToken = 'your_bitwarden_api_token';

    public static function query($query)
    {
        $results = self::searchBitwarden($query);

        $items = [];
        foreach ($results as $result) {
            $items[] = [
                "Title" => $result["name"],
                "SubTitle" => "Copy username and password",
                "Score" => 100,
                "IcoPath" => "path/to/icon.png",
                "JsonRPCAction" => [
                    "method" => "CopyToClipboard",
                    "parameters" => [$result["username"], $result["password"]]
                ]
            ];
        }

        return ["result" => $items];
    }

    private static function searchBitwarden($query)
    {
        $url = self::$apiUrl . 'items?search=' . urlencode($query);

        // Set up cURL options
        $curlOptions = [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . self::$apiToken,
                'Content-Type: application/json',
            ],
            CURLOPT_RETURNTRANSFER => true,
        ];

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);

        // Execute cURL session and get the response
        $response = curl_exec($ch);

        // Close cURL session
        curl_close($ch);

       
        $decodedResponse = json_decode($response, true);

      
        $items = [];

        foreach ($decodedResponse as $item) {
            $items[] = [
                "name" => isset($item['name']) ? $item['name'] : '',
                "username" => isset($item['login']['username']) ? $item['login']['username'] : '',
                "password" => isset($item['login']['password']) ? $item['login']['password'] : '',
            ];
        }

        return $items;
    }
}

?>
