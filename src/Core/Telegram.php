<?php

namespace TelegramWooNotifications\Core;

class Telegram {
    
    public static function post_to_telegram($message, $title, $method = 'sendMessage') {
        
        $telegram_bot_token = get_option( 'telegram_bot_token' );

        $url = 'https://api.telegram.org/bot' . $telegram_bot_token . '/' . $method;

        $formatted_message = strip_tags($message, '<br>'); //phpcs:ignore

        $response = wp_remote_post( $url, array(
            'method'      => 'POST',
            'timeout'     => 3,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => array(
                'parse_mode'  => 'HTML',
                'text' => "<b>" . $title ."</b> \n" . str_replace("<br />", "\n", $formatted_message),
                'chat_id' => '83988429',
            )
        ));

        if ( 200 !== $response['response']['code'] ) {
        
            $error_message = 'Telegram bot failed.  Error: ' . $response['body'];
    
            $response = wp_remote_post( $url, array(
                'method'      => 'POST',
                'timeout'     => 3,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking'    => true,
                'headers'     => array(),
                'body'        => array(
                    'text' => $error_message,
                    'chat_id' => '83988429',
                )
            ));
        }
    }
}
