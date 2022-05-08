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

        $user = get_user_by('email', $data['email']);
        dd($user);

        // update the data in wordpress
        wp_update_user([
            'email'
        ]);

        $log->info('webhook', $data);
    }
}

$user = is_user_logged_in();
dd($user);
