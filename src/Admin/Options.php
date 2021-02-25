<?php

namespace TelegramWooNotifications\Admin;

class Options {
    
    public function __construct() {
        add_action( 'admin_menu', array($this, 'register_page' ) );
        add_action( 'admin_head', array($this, 'enqueue_scripts'));
        add_action( 'admin_init', array($this, 'register_settings'));
    }
    
    public function enqueue_scripts() {
        if ( class_exists( 'woocommerce' ) ) {
            // WooCommerce Stuff
            wp_enqueue_script( 'selectWoo' );
            wp_enqueue_style( 'select2-telegram-woo-notifications', WC()->plugin_url() . '/assets/css/select2.css', array(), TELEGRAM_WOO_NOTIFICATIONS_VERSION );
        }
        
        wp_enqueue_script( 'main.js', plugin_dir_url( __DIR__ ) . 'assets/js/main.js', 'selectWoo', 1.0, true );
        
        wp_localize_script( 'main.js', 'telegram_bot_token', array(
            'data' => get_option( 'telegram_bot_token' ),
        ));
    }

    public function register_page() {
       add_submenu_page( 'options-general.php', 'Telegram for Woo', 'Telegram for Woo', 'manage_options', 'telegram-woo-notifications',  array( $this, 'telegram_woo_notifications_callback') );
    }

    public function register_settings() {
        register_setting( 'telegram_woo_notifications', 'telegram_bot_token' );
        register_setting( 'telegram_woo_notifications', 't4wn_woo_categories_setting' );
        register_setting( 'telegram_woo_notifications', 't4wn_jp_cf_setting' );
        register_setting( 'telegram_woo_notifications', 't4wn_woo_status_setting' );

        // General Settings
        add_settings_section(
            't4wn_general_settings_section',
            'General', 
            array( $this, 't4wn_general_settings_section_callback' ),
            'telegram_woo_notifications'
        );

        add_settings_field(
            'telegram_bot_token',
            'Telegram Bot Token', 
            array( $this, 'telegram_bot_token' ),
            'telegram_woo_notifications',
            't4wn_general_settings_section'
        );

        // WooCommerce Settings
        add_settings_section(
            't4wn_woo_settings_section',
            'WooCommerce Options', 
            array( $this, 't4wn_woo_settings_section_callback' ),
            'telegram_woo_notifications'
        );

        add_settings_field(
            't4wn_woo_categories_setting',
            'Choose Categories', 
            array( $this, 't4wn_woo_categories_setting_callback' ),
            'telegram_woo_notifications',
            't4wn_woo_settings_section'
        );

        add_settings_field(
            't4wn_woo_status_setting',
            'Choose Order Status', 
            array( $this, 't4wn_woo_status_setting_callback' ),
            'telegram_woo_notifications',
            't4wn_woo_settings_section'
        );
    }

    public function t4wn_general_settings_section_callback() {
        echo '<a target="_blank" href="https://core.telegram.org/bots#6-botfather">Create a Telegram Bot</a> and add your Token below. <br />';
    }

    public function t4wn_woo_settings_section_callback() {
        echo "<p>Select which categories and order statuses you want to receive alerts for.  <br /> When an order is created or updated, a Telegram notifications will be sent if the order has a matching category or status below.<br /> If you want to get notifications for all orders or categories, select the \"All\" option.</p>";
    }

    public function telegram_bot_token() {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option( 'telegram_bot_token' );
        ?>
        <input class="telegram_bot_token_class" type="text" style="width:50%" name="telegram_bot_token" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <p class="test-token">
            <input type="button"     action="#" value="Test Token" id="test-token">
        </p>
        <?php
    }

    public function t4wn_woo_categories_setting_callback() {
        $category_ids = get_option( 't4wn_woo_categories_setting' );
        $categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );

        ?>
        <select id="product_categories" name="t4wn_woo_categories_setting[]" class="woo-category wc-enhanced-select" multiple="multiple" style="width: 50%;" ?>" >
        <?php

        if ( $categories ) {
            foreach ( $categories as $cat  ) {
                if ( is_object( $cat )) {
                    echo '<option value="' . esc_attr( $cat->term_id ) . '"' . wc_selected( $cat->term_id, $category_ids ) . '>' . esc_html( $cat->name ) . '</option>'; //phpcs:ignore
                }
            }
        }
        ?>
        </select>
        <?php
    }
    public function t4wn_woo_status_setting_callback() {
        $statuses = get_option( 't4wn_woo_status_setting' );
        $wc_order_statuses = wc_get_order_statuses();
        ?>
        <select id="product_categories" name="t4wn_woo_status_setting[]" class="woo-category wc-enhanced-select" multiple="multiple" style="width: 50%;" ?>" >
        <?php
        if ( $wc_order_statuses ) {
            foreach ( $wc_order_statuses as $key => $status  ) {
                echo '<option value="' . esc_attr( $key ) . '"' . wc_selected( $key, $statuses ) . '>' . esc_html( $status ) . '</option>'; //phpcs:ignore
            }
        }
    }

    public function telegram_woo_notifications_callback() {
        ?>
        <form method="post" action="options.php" enctype="multipart/form-data">
        <?php

        echo '<div class="wrap">';
        echo '<h2>Telegram for Woo</h2>';
        echo '</div>';
        settings_fields( 'telegram_woo_notifications' );
        do_settings_sections( 'telegram_woo_notifications' );
        submit_button();
        ?>
        </form>
        <?php
    }

}
