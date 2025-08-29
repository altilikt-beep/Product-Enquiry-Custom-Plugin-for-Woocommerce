<?php
/**
 * Plugin Name: Product Inquiry Downloadable
 * Description: Adds a Product Inquiry button (before Add to Cart). Popup captures product info and Name/Email/Phone (intl phone with flags). Saves inquiries, admin list + CSV export, emails admin & customer.
 * Version: 1.0.0
 * Author: ChatGPT
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'PID_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PID_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once PID_PLUGIN_DIR . 'includes/pid-functions.php';
require_once PID_PLUGIN_DIR . 'includes/pid-ajax.php';
require_once PID_PLUGIN_DIR . 'includes/pid-admin.php';

// Enqueue assets and intl-tel-input from CDN
add_action( 'wp_enqueue_scripts', function(){
    wp_enqueue_style( 'pid-intlcss', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css' );
    wp_enqueue_script( 'pid-intljs', 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js', array('jquery'), null, true );
    wp_enqueue_style( 'pid-style', PID_PLUGIN_URL . 'assets/css/pid-style.css' );
    wp_enqueue_script( 'pid-js', PID_PLUGIN_URL . 'assets/js/pid.js', array('jquery'), null, true );
    wp_localize_script( 'pid-js', 'pid_ajax', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'pid_submit' ),
        'utils_script' => 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js'
    ) );
} );


// Create DB table on activation
register_activation_hook( __FILE__, 'pid_create_table' );
function pid_create_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'pid_inquiries';
    $charset = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        product_id bigint(20) DEFAULT 0,
        product_name varchar(255) DEFAULT '',
        product_image text,
        name varchar(150) DEFAULT '',
        email varchar(150) DEFAULT '',
        phone varchar(80) DEFAULT '',
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    ) $charset;";
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
