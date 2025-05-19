<?php
/*
Plugin Name: Signup Storage
Description: Slaat e-mailadressen op van een custom inschrijfformulier.
Version: 1.0
Author: Mert Korkmaz
*/

defined('ABSPATH') || exit;


register_activation_hook(__FILE__, function () {
    global $wpdb;
    $table = $wpdb->prefix . 'signup_emails';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        id INT NOT NULL AUTO_INCREMENT,
        email VARCHAR(255) NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
});


add_action('wp_ajax_save_signup_email', 'save_signup_email');
add_action('wp_ajax_nopriv_save_signup_email', 'save_signup_email');

function save_signup_email() {
    if (!isset($_POST['email']) || !is_email($_POST['email'])) {
        wp_send_json_error('Ongeldig e-mailadres');
    }

    global $wpdb;
    $email = sanitize_email($_POST['email']);
    $table = $wpdb->prefix . 'signup_emails';

    $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE email = %s", $email));
    if ($exists > 0) {
        wp_send_json_error('E-mailadres is al geregistreerd.');
    }

    $wpdb->insert($table, ['email' => $email]);

    wp_send_json_success('Bedankt voor je inschrijving!');
}


add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('signup-form-ajax', plugin_dir_url(__FILE__) . 'signup.js', ['jquery'], null, true);
    wp_localize_script('signup-form-ajax', 'signup_ajax_obj', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('signup_nonce')
    ]);

    wp_enqueue_style('signup-form-style', plugin_dir_url(__FILE__) . 'style.css');
});


function render_signup_form_shortcode() {
    ob_start(); ?>
    <form class="signup-form">
        <input type="email" placeholder="Your email" required>
        <button type="submit">Sign Up</button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('signup_form', 'render_signup_form_shortcode');


add_action('admin_menu', function () {
    add_menu_page(
        'Inschrijvingen',
        'Inschrijvingen',
        'manage_options',
        'signup-storage',
        'render_signup_admin_page',
        'dashicons-email-alt2',
        25
    );
});

function render_signup_admin_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'signup_emails';
    $results = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

    echo '<div class="wrap"><h1>Ingeschreven E-mailadressen</h1>';
    if ($results) {
        echo '<table class="widefat fixed" style="max-width:800px;"><thead><tr><th>#</th><th>E-mailadres</th><th>Datum</th></tr></thead><tbody>';
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row->id) . '</td>';
            echo '<td>' . esc_html($row->email) . '</td>';
            echo '<td>' . esc_html($row->created_at) . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<p>Er zijn nog geen inschrijvingen.</p>';
    }
    echo '</div>';
}
