<?php 
namespace TelegramWooNotifications\Jetpack;
use TelegramWooNotifications\Core\Telegram;

// Send Jetpack Contact Form submissions to Telegram Bot
class ContactForm {
    
    public function __construct() {
        add_filter('contact_form_message', array($this, 'send_jp_cf_to_telegram'));
    }
    public static function send_jp_cf_to_telegram($message) {
        
        if ( 1 == get_option('telegramforwoo_jp_cf_setting') ) {
            Telegram::post_to_telegram($message, 'New Message');
        };
        return true;
    }
}
