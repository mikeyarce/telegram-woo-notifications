<?php

namespace TelegramWooNotifications\Admin;

class Options {
    
    public function __construct() {
        add_action( 'admin_menu', array($this, 'register_page' ) );
        add_action( 'admin_head', array($this, 'enqueue_scripts'));
        add_action( 'admin_init', array($this, 'register_settings'));
    }
    public function enqueue_scripts() {
        // WooCommerce Stuff
        wp_enqueue_script( 'selectWoo' );
        wp_enqueue_style( 'select2-woo-to-telegram', WC()->plugin_url() . '/assets/css/select2.css', array(), WC_VERSION );
        
        wp_enqueue_script( 'main.js', plugin_dir_url( __DIR__ ) . 'assets/js/main.js', 'selectWoo', 1.0, true );
        
        wp_localize_script( 'main.js', 'telegram_bot_token', array(
            'data' => get_option( 'telegram_bot_token' ),
        ));
    }

    public function register_page() {
       add_submenu_page( 'options-general.php', 'Telegram for Woo', 'Telegram for Woo', 'manage_options', 'telegramforwoo',  array( $this, 'telegramforwoo_callback') );
    }

    public function register_settings() {
        register_setting( 'telegramforwoo', 'telegram_bot_token' );
        register_setting( 'telegramforwoo', 'telegramforwoo_woo_categories_setting' );
        register_setting( 'telegramforwoo', 'telegramforwoo_jp_cf_setting' );

        // General Settings
        add_settings_section(
            'telegramforwoo_general_settings_section',
            'General', 
            array( $this, 'telegramforwoo_general_settings_section_callback' ),
            'telegramforwoo'
        );

        add_settings_field(
            'telegram_bot_token',
            'Telegram Bot Token', 
            array( $this, 'telegram_bot_token' ),
            'telegramforwoo',
            'telegramforwoo_general_settings_section'
        );

        // WooCommerce Settings
        add_settings_section(
            'telegramforwoo_woo_settings_section',
            'WooCommerce Options', 
            array( $this, 'telegramforwoo_woo_settings_section_callback' ),
            'telegramforwoo'
        );

        add_settings_field(
            'telegramforwoo_woo_categories_setting',
            'Choose Categories', 
            array( $this, 'telegramforwoo_woo_categories_setting_callback' ),
            'telegramforwoo',
            'telegramforwoo_woo_settings_section'
        );

        add_settings_field(
            'telegramforwoo_woo_status_setting',
            'Choose Order Status', 
            array( $this, 'telegramforwoo_woo_status_setting_callback' ),
            'telegramforwoo',
            'telegramforwoo_woo_settings_section'
        );
    }

    public function telegramforwoo_general_settings_section_callback() {
        echo '<a target="_blank" href="https://core.telegram.org/bots#6-botfather">Create a Telegram Bot</a> and add your Token below. <br />';
    }
    
    public function telegramforwoo_woo_settings_section_callback() {
        echo "Select which categories you want to receive alerts for.  <br /> When an order is placed that contains a product with any of the selected categories, you will receive a Telegram message with the order details.";
    }

    public function telegramforwoo_jp_cf_setting_callback() {
        ?>
        <input type="checkbox" name="telegramforwoo_jp_cf_setting" value="1" <?php checked(1, get_option('telegramforwoo_jp_cf_setting'), true); ?> /> 
        <?php
    }

    public function telegram_bot_token() {
        // get the value of the setting we've registered with register_setting()
        $setting = get_option( 'telegram_bot_token' );
        ?>
        <input type="text" style="width:50%" name="telegram_bot_token" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
        <p class="test-token">
            <input type="button"     action="#" value="Test Token" id="test-token">
        </p>
        <?php
    }

    public function telegramforwoo_woo_categories_setting_callback() {
        $category_ids = get_option( 'telegramforwoo_woo_categories_setting' );
        $categories   = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
                
        ?>
        <select id="product_categories" name="telegramforwoo_woo_categories_setting[]" class="woo-category wc-enhanced-select" multiple="multiple" style="width: 50%;" ?>" >
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
    public function telegramforwoo_woo_status_setting_callback() {
        $status = get_option( 'telegramforwoo_woo_status_setting' );
        
    }

    public function telegramforwoo_callback() {
        ?>
        <form method="post" action="options.php" enctype="multipart/form-data">
        <?php

        echo '<div class="wrap">';
        echo '<h2>Telegram for Woo</h2>';
        echo '</div>';
        settings_fields( 'telegramforwoo' );
        do_settings_sections( 'telegramforwoo' );
        submit_button();
        ?>
        </form>
        <?php
    }

}
