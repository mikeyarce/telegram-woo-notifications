<?php

namespace TelegramWooNotifications\WooCommerce;
use TelegramWooNotifications\Core\Telegram;

class OrderActions {

    public function __construct() {
        add_action( 'woocommerce_order_status_processing', array( $this, 'send_to_telegram' ) );
    }
    
    private function get_order_details($order_id) {
        // When a new order happens, get the order
        $order_object = wc_get_order($order_id);
        return $order_object;
    }
    private function get_categories() {
        return get_option( 'telegramforwoo_woo_categories_setting' );
    }
    private function has_valid_categories($product_array) {
        $category_ids = $this->get_categories();
        $product_categories = array();

        foreach ( $product_array as $product_id ) {
            $product_cat_array = $product_id->get_category_ids();
            $product_categories = array_merge($product_categories, $product_cat_array);
        }
    
        if ( !empty( array_intersect($category_ids, $product_categories ) ) ) {
            return true;
        }
    }
    private function get_status() {
        
    }
    private function get_valid_status() {
        
    }
    public function format_order_message($order_id) {
        $order_object = $this->get_order_details($order_id);
        $product_array = array();
        
        $order_shipping_methods = $order_object->get_shipping_methods();
        $order_items = $order_object->get_items();

        
        $text = '';
        $text .= "Order ID: " . $order_id . " \n";
        $text .= "Order Created: " . $order_object->get_date_modified() . " \n";
        $text .= "Order Modified: " . $order_object->get_date_modified() . " \n";
        $text .= "Customer Info: " . implode(' ', $order_object->get_address() ) . " \n";
        foreach ( $order_items as $item_id => $item ) {
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
        $text .= "\n Customer Note: " . $order_object->get_customer_note() . " \n";

        if ( true === $this->has_valid_categories($product_array) ) {
            return $text;
        }
    }
    public function send_to_telegram($order_id) {
        $order_details = $this->format_order_message($order_id);
        Telegram::post_to_telegram($order_details, 'New Order');
    }
}