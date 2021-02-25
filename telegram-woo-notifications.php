<?php

/**
 * Plugin Name: Telegram Woo Notifications
 * Plugin URI: https://mikeyarce.com
 * Description: Integrates your WooCommerce store with Telegram!
 * Author: mikeyarce
 * Author URI: https://mikeyarce.com
 * Version: 1.0.0
 * Requires at least: 4.4
 * Tested up to: 5.2.1
 * WC requires at least: 3.0
 * WC tested up to: 3.6.4
 * Text Domain: telegram-woo-notifications
 * Domain Path: /languages
 *
 */
namespace TelegramWooNotifications;

define( 'TELEGRAM_WOO_NOTIFICATIONS_VERSION', '1.0.0' );
if ( file_exists( __DIR__ . '/vendor/autoload.php' )){
    require_once __DIR__ . '/vendor/autoload.php';
}

use TelegramWooNotifications\Admin\Options;
use TelegramWooNotifications\Jetpack\ContactForm;
use TelegramWooNotifications\WooCommerce\OrderActions;


$options = new Options();
$contact_form = new ContactForm();
$order_actions = new OrderActions();
