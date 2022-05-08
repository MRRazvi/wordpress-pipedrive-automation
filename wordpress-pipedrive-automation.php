<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin Name:       WordPress Pipedrive Automation
 * Description:       It is a simple plugin which will provide two-way syncing between wordpress user and pipedrive person, and additionally it will support woocommerce membership and subscription syncing as well.
 * Version:           1.0.0
 * Author:            Mubashir Rasool Razvi
 * Author URI:        https://www.upwork.com/freelancers/~01ef7b2184f920ecf7
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       rizimore_wpa
 */

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$log = new Logger('rizimore_wpa');
$log->pushHandler(new StreamHandler(__DIR__ . '/logs/app.log', Logger::INFO));

$client = new Pipedrive\Client(null, null, null, $_ENV['PIPEDRIVE_KEY']);

require_once __DIR__ . '/src/webhooks/wordpress.php';
require_once __DIR__ . '/src/webhooks/pipedrive.php';
