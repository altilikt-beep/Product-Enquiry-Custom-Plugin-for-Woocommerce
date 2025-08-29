<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// AJAX handlers
add_action( 'wp_ajax_pid_submit', 'pid_handle_submit' );
add_action( 'wp_ajax_nopriv_pid_submit', 'pid_handle_submit' );

function pid_handle_submit() {
    if ( ! isset($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'pid_submit' ) ) {
        wp_send_json_error( 'Security check failed.' );
    }

    global $wpdb;
    $table = $wpdb->prefix . 'pid_inquiries';

    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $product_name = isset($_POST['product_name']) ? sanitize_text_field( $_POST['product_name'] ) : '';
    $product_image = isset($_POST['product_image']) ? esc_url_raw( $_POST['product_image'] ) : '';
    $name = isset($_POST['name']) ? sanitize_text_field( $_POST['name'] ) : '';
    $email = isset($_POST['email']) ? sanitize_email( $_POST['email'] ) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field( $_POST['phone'] ) : '';

    if ( empty($name) || empty($email) || empty($phone) ) {
        wp_send_json_error( 'Please fill required fields.' );
    }

    $inserted = $wpdb->insert( $table, array(
        'product_id' => $product_id,
        'product_name' => $product_name,
        'product_image' => $product_image,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'created_at' => current_time('mysql')
    ), array('%d','%s','%s','%s','%s','%s','%s') );

    if ( false === $inserted ) {
        wp_send_json_error( 'Database error.' );
    }

    // Send admin email
    $admin_email = get_option('admin_email');
    $subject_admin = 'New Product Inquiry: ' . $product_name;
    $body_admin = '<h2>New Product Inquiry</h2>';
    $body_admin .= '<p><strong>Product:</strong> ' . esc_html( $product_name ) . ' (ID: ' . intval($product_id) . ')</p>';
    if ( $product_image ) {
        $body_admin .= '<p><img src="' . esc_url( $product_image ) . '" alt="" style="max-width:200px;height:auto;"></p>';
    }
    $body_admin .= '<p><strong>Name:</strong> ' . esc_html( $name ) . '</p>';
    $body_admin .= '<p><strong>Email:</strong> ' . esc_html( $email ) . '</p>';
    $body_admin .= '<p><strong>Phone:</strong> ' . esc_html( $phone ) . '</p>';

    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $admin_email, $subject_admin, $body_admin, $headers );

    // Send confirmation to customer
    $subject_user = 'We received your inquiry about ' . $product_name;
    $body_user = '<p>Hi ' . esc_html( $name ) . ',</p>';
    $body_user .= '<p>Thanks for your inquiry about <strong>' . esc_html( $product_name ) . '</strong>. We will contact you shortly.</p>';
    $body_user .= '<p>Best regards,<br>' . get_bloginfo('name') . '</p>';

    wp_mail( $email, $subject_user, $body_user, $headers );

    wp_send_json_success( 'Thank you — your inquiry has been saved.' );
}
