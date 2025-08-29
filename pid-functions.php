<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Render button BEFORE Add to Cart
add_action( 'woocommerce_before_add_to_cart_button', 'pid_render_button', 9 );
function pid_render_button() {
    if ( ! function_exists('is_product') || ! is_product() ) return;
    global $product;
    if ( ! $product ) return;
    $pid = $product->get_id();
    $pname = $product->get_name();
    $image = $product->get_image_id() ? wp_get_attachment_image_url( $product->get_image_id(), 'medium' ) : wc_placeholder_img_src();
    printf( '<button type="button" class="pid-inquiry-btn" data-product-id="%1$s" data-product-name="%2$s" data-product-image="%3$s">%4$s</button>',
        esc_attr($pid), esc_attr($pname), esc_url($image), esc_html__('Product Inquiry','pid-domain') );
}

// Print modal once in footer
add_action( 'wp_footer', 'pid_print_modal' );
function pid_print_modal() {
    if ( ! function_exists('is_product') || ! is_product() ) return;
    include PID_PLUGIN_DIR . 'templates/modal.php';
}
