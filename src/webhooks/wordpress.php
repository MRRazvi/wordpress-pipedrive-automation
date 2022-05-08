<?php

add_action('user_register', 'rizimore_wpa_registration_save', 10, 1);

function rizimore_wpa_registration_save($user_id) {
    global $log, $client;

    $data = [
        'id' => $user_id,
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'username' => $_POST['user_login'],
        'email' => $_POST['email']
    ];

    try {
        $name = empty(trim(sprintf('%s %s', $data['first_name'], $data['last_name']))) ? $data['username'] : trim(sprintf('%s %s', $data['first_name'], $data['last_name']));

        $person = $client->getPersons()->addAPerson([
            'name' => $name,
            'email' => $data['email']
        ]);
    } catch (Exception $e) {
        $log->error($e->getMessage());
    }
}
