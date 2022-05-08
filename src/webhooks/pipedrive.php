<?php

if (isset($_GET['webhook'])) {
    if ($_GET['webhook'] == 'rizimore_wpa') {
        global $log;

        if($json = json_decode(file_get_contents("php://input"), true)) {
            $data = $json;
        } else {
            $data = $_POST;
        }

        // get the data
        $data = [
            'id' => $data['meta']['id'],
            'first_name' => $data['current']['first_name'],
            'last_name' => $data['current']['last_name'],
            'email' => $data['current']['email'][0]['value']
        ];

        // update the user data

        $log->info('webhook', $data);
    }
}
