<?php

namespace TelegramWooNotifications\WooCommerce;
use TelegramWooNotifications\Core\Telegram;

class OrderActions {

    public $statuses;
    public $categories;

    public function __construct() {

        $this->add_actions_for_statuses();
        $this->get_order_statuses_for_notifications();
        $this->get_categories();
        
    }

    public function add_actions_for_statuses() {
        add_action( 'woocommerce_order_status_changed', function( $order_id, $status_from, $status_to ) {
            if ( ! empty( $order_id ) && $this->validate_order_items( $order_id ) && $this->validate_order_status( $status_to ) ) {
                $this->send_to_telegram( $order_id );
            }           
        }, 10, 3 );
    }

    private function get_order_categories( $order_id ) {
        $order = new \WC_Order( $order_id );
        $items = $order->get_items();
        $categories = array();

        foreach ($items as $item ) {
            $product = $item->get_product();
            $categories = $product->get_category_ids();
        }
        return $categories;
    }

    private function validate_order_items( $order_id ) {
        $order_categories = $this->get_order_categories( $order_id );
        if ( array_intersect( $order_categories, $this->categories ) ) {
            return true;
        }
    }

    private function parse_order_statuses( $statuses ) {
        if ( $statuses && is_array( $statuses ) ) {
            foreach ( $statuses as $key => $status ) {
                $statuses[$key] = substr( $status, 3 );
            }
        }
        return $statuses;
    }

    public function get_order_statuses_for_notifications() {
        $statuses = $this->parse_order_statuses( get_option( 't4wn_woo_status_setting' ) ); 
        $this->statuses = $statuses;
    }

    private function validate_order_status( $status ) {
        if ( in_array( $status, $this->statuses, true ) ) {
            return true;
        }            
    }

    private function get_order_details($order_id) {
        // When a new order happens, get the order
        $order_object = wc_get_order($order_id);
        return $order_object;
    }

    public function get_categories() {
        // Add Filter to exclude categories maybe?
        $this->categories = get_option( 't4wn_woo_categories_setting' );
    }

    private function format_order_message( $order_id ) {
        $order_object = $this->get_order_details($order_id);
        $product_array = array();
        
        $order_shipping_methods = $order_object->get_shipping_methods();
        $order_items = $order_object->get_items();

        $date_format = get_option( 'date_format' );
        $time_format = get_option( 'time_format' );

        $text = '';
        $text .= "Order ID: " . $order_id . " \n";
        $text .= "Order Created: " . $order_object->get_date_created()->date( $date_format . ', ' .  $time_format ) . " \n";
        $text .= "Order Modified: " . $order_object->get_date_modified()->date($date_format . ', ' .  $time_format) . " \n";
        $text .= "Customer Info: " . implode(' ', $order_object->get_address() ) . " \n";
        foreach ( $order_items as $item ) {
            $category_names = '';
            $product = $item->get_product();
            $product_array[] = $product;

            $product_title = $item->get_name();
            $product_quantity = $item->get_quantity();
            $parent_id = $product->get_parent_id();

            if ( 0 !== $parent_id ) { 
                $parent_product = wc_get_product( $parent_id );
                $product_categories = $parent_product->get_category_ids();
            } else {
                $product_categories = $product->get_category_ids();
            }
            foreach ( $product_categories as $cat ) {
                $categories = get_term_by( 'id', $cat, 'product_cat');
                $category_names .= $categories->name . " ";
            }

            $product_subtotal = $item->get_subtotal();

            $text .= "Item: " . $product_title . " QTY: " . $product_quantity . " Subtotal: " . $product_subtotal . " Categories: " . $category_names. "\n";
        }

        foreach ( $order_shipping_methods as $shipping_method ) {
            $text .= "Shipping: " . $shipping_method->get_method_title() . " Total: " . $shipping_method->get_total() . " \n";
            
        }
        $text .= "Payment Method: " . $order_object->get_payment_method_title() . " \n";
        $customer_note = ! empty( $order_object->get_customer_note() ) ? "\n Customer Note: " . $order_object->get_customer_note() . " \n" : '';
        $text .= $customer_note;

        return esc_html( $text );
    }

    private function send_to_telegram($order_id) {
        $order_details = $this->format_order_message($order_id);
        $message_title = ! empty( get_option( 't4wn_telegram_message_title' ) ) ? esc_html( get_option( 't4wn_telegram_message_title' ) ) : 'Order Notification';
        error_log( print_r( $message_title, true ));
        Telegram::post_to_telegram($order_details, $message_title );
    }
}