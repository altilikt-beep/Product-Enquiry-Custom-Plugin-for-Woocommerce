<?php
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', 'pid_admin_menu' );
function pid_admin_menu() {
    add_menu_page( 'Product Inquiries', 'Product Inquiries', 'manage_options', 'pid-inquiries', 'pid_admin_page', 'dashicons-email-alt', 56 );
}

function pid_admin_page() {
    if ( ! current_user_can('manage_options') ) wp_die('Insufficient permissions');

    global $wpdb;
    $table = $wpdb->prefix . 'pid_inquiries';
    $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC" );

    $export_url = wp_nonce_url( admin_url('admin-post.php?action=pid_export_csv'), 'pid_export_nonce' );
    ?>
    <div class="wrap">
      <h1>Product Inquiries</h1>
      <p><a class="button button-primary" href="<?php echo esc_url($export_url); ?>">Export CSV</a></p>
      <table class="widefat fixed striped">
        <thead><tr><th>ID</th><th>Product</th><th>Image</th><th>Name</th><th>Email</th><th>Phone</th><th>Date</th></tr></thead>
        <tbody>
        <?php if ( $rows ) {
            foreach ( $rows as $r ) {
                echo '<tr>';
                echo '<td>'.intval($r->id).'</td>';
                echo '<td>'.esc_html($r->product_name).' ('.intval($r->product_id).')</td>';
                echo '<td>';
                if ( $r->product_image ) echo '<img src="'.esc_url($r->product_image).'" style="max-width:80px;height:auto;">';
                echo '</td>';
                echo '<td>'.esc_html($r->name).'</td>';
                echo '<td>'.esc_html($r->email).'</td>';
                echo '<td>'.esc_html($r->phone).'</td>';
                echo '<td>'.esc_html($r->created_at).'</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="7">No inquiries found.</td></tr>';
        } ?>
        </tbody>
      </table>
    </div>
    <?php
}

// CSV export handler
add_action( 'admin_post_pid_export_csv', 'pid_export_csv' );
function pid_export_csv() {
    if ( ! current_user_can('manage_options') ) wp_die('Insufficient permissions');
    if ( ! check_admin_referer('pid_export_nonce') ) wp_die('Security check failed');

    global $wpdb;
    $table = $wpdb->prefix . 'pid_inquiries';
    $rows = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at DESC", ARRAY_A );
    if ( ! $rows ) wp_redirect( admin_url('admin.php?page=pid-inquiries') );

    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=product-inquiries-'.date('Y-m-d').'.csv');
    $output = fopen('php://output','w');
    fputcsv($output, array_keys($rows[0]));
    foreach($rows as $row) fputcsv($output, $row);
    fclose($output);
    exit;
}
