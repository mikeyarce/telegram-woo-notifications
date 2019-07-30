<?php

/**
 * Plugin Name: Telegram WP Integrations
 * Plugin URI: https://mikeyarce.com
 * Description: Integrates your site with Telegram!  Currently can send Jetpack Contact Form submissions and new WooCommerce orders to Telegram.
 * Author: mikeyarce
 * Author URI: https://mikeyarce.com
 * Version: 1.0.0
 * Requires at least: 4.4
 * Tested up to: 5.2.1
 * WC requires at least: 3.0
 * WC tested up to: 3.6.4
 * Text Domain: telegram-wp-integrations
 * Domain Path: /languages
 *
 */
namespace TelegramWPIntegrations;

if ( file_exists( __DIR__ . '/vendor/autoload.php' )){
    require_once __DIR__ . '/vendor/autoload.php';
}

use TelegramWPIntegrations\Admin\Options;
use TelegramWPIntegrations\Jetpack\ContactForm;
use TelegramWPIntegrations\WooCommerce\OrderActions;


$options = new Options();
$contact_form = new ContactForm();
$order_actions = new OrderActions();
