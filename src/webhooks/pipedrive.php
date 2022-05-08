<?php

if (isset($_GET['webhook'])) {
    if ($_GET['webhook'] == 'rizimore_wpa') {
        global $log;

        if($json = json_decode(file_get_contents("php://input"), true)) {
            $data = $json;
        } else {
            $data = $_POST;
        }

        $log->info('webhook', [$data['meta']['id']]);

        // get the data
        $data = [
            'id' => $data['meta']['id'],
            'first_name' => $data['current']['first_name'],
            'last_name' => $data['current']['last_name'],
            'name' => $data['current']['name'],
            'email' => $data['current']['email'][0]['value']
        ];

        // update the user data
        $user = get_user_by('email', $data['email']);
        if (!$user) {
            $log->error('user_not_found', $data);
        }

        wp_update_user([
            'ID' => $user->id,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'display_name' => $data['name']
        ]);
    }
}
