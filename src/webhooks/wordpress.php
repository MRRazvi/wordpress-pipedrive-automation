<?php

// user created sync
add_action('user_register', function ($user_id, $userdata) {
    global $log, $client;

    $data = [
        'first_name' => $userdata['first_name'],
        'last_name' => $userdata['last_name'],
        'username' => $userdata['user_login'],
        'email' => $userdata['user_email']
    ];

    try {
        $name = empty(trim(sprintf('%s %s', $data['first_name'], $data['last_name']))) ? $data['username'] : trim(sprintf('%s %s', $data['first_name'], $data['last_name']));

        $person = $client->getPersons()->addAPerson([
            'name' => $name,
            'email' => $data['email']
        ]);

        add_user_meta($user_id, 'pipedrive_person_id', $person->jsonSerialize()->data->id, true);
        rizimore_wpa_update_user(WC()->customer);
    } catch (Exception $e) {
        $log->error($e->getMessage());
    }
}, 10, 2);


// user updated sync
add_action('profile_update', function ($user_id) {
    rizimore_wpa_update_user(new WC_Customer($user_id));
}, 10, 2);

// subscription created sync
add_action('woocommerce_checkout_subscription_created', function ($subscription) {
    global $log, $client;

    $data = [
        'pipedrive_person_id' => get_user_meta($subscription->get_customer_id(), 'pipedrive_person_id', true),
        'subscription_id' => $subscription->get_id(),
        'status' => $subscription->get_status(),
        'currency' => $subscription->get_currency(),
        'total' => $subscription->get_total(),
        'next_payment' => $subscription->calculate_date('next_payment'),
        'billing_period' => $subscription->get_billing_period(),
    ];

    foreach ($subscription->get_items() as $item) {
        $data['items'] .= sprintf('%s, ', $item->get_name());
    }

    try {
        if ($data['pipedrive_person_id']) {
            $client->getDeals()->addADeal([
                'title' => sprintf('Subscription #%s', $data['subscription_id']),
                'person_id' => $data['pipedrive_person_id'],
                'currency' => $data['currency'],
                'value' => $data['total'],
                'expected_close_date' => empty($data['next_payment']) ? '' : date(DATE_ISO8601, $data['next_payment']),
                '43449878f940d867b60bea8c68a4ca60d4f67702' => $data['status'],
                '1b015af1745b812ae604062b3d152d2cc89db50e' => $data['items'],
                'aa669c9f7698729c61b1b36ca50afbb3536b4222' => $data['billing_period'],
            ]);
        }
    } catch (Exception $e) {
        $log->error($e->getMessage());
    }
}, 10, 2);


// subscription updated sync

function rizimore_wpa_update_user($user) {
    global $log, $client;

    $data = [
        'pipedrive_person_id' => get_user_meta($user->get_id(), 'pipedrive_person_id', true),
        'username' => $user->get_username(),
        'email' => $user->get_email(),
        'full_name' => $user->get_display_name(),
        'billing_address_1' => $user->get_billing_address_1(),
        'billing_city' => $user->get_billing_city(),
        'billing_state' => $user->get_billing_state(),
        'billing_postcode' => $user->get_billing_postcode(),
        'billing_country' => $user->get_billing_country(),
        'billing_phone' => $user->get_billing_phone(),
    ];

    try {
        if ($data['pipedrive_person_id']) {
            $client->getPersons()->updateAPerson([
                'id' => $data['pipedrive_person_id'],
                'name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $data['billing_phone'],
                '6eb6cf84d445875ff88bd8a16bdf180afb24dd6c' => $data['username'],
                'f9a57429c7cbfd0a4b719353a661ef0482379fad' => $data['billing_address_1'],
                '2470a0ec8f888b8205361298c2c902235f9310f9' => $data['billing_city'],
                '332f41ab61b5cabd8fb4a305d7edca118cff6ab9' => $data['billing_state'],
                'f58ea46b8ad0df1dbd556803248d56f8d06adf1a' => $data['billing_postcode'],
                '9d1bd3a606f579a571a4ca8bb319029b79c1d73f' => $data['billing_country'],
            ]);
        }
    } catch (Exception $e) {
        $log->error($e->getMessage());
    }
}
