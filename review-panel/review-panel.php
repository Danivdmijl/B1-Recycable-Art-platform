<?php
/**
 * Plugin Name: Account Review Panel
 * Plugin URI: https://www.linkedin.com/in/dani-van-der-mijl/
 * Description: A plugin that takes all the new registerd users and put them in a review queue
 * Version: 1.0
 * Author: Dani van der Mijl
 * Author URI: https://www.linkedin.com/in/dani-van-der-mijl/
 * License: GPL2
 */
?>

<?php
// Add a menu item for the plugin
function account_review_add_admin_menu() {
	add_menu_page(
    	'Account Review Panel',   
    	'Account Review',        
    	'manage_options',         
    	'account-review-panel',   
    	'account_review_settings_page' 
	);
}
add_action('admin_menu', 'account_review_add_admin_menu');

// Create the settings page content
function account_review_settings_page() {
	// Handle Approve or Deny actions
	if (isset($_GET['action']) && isset($_GET['user_id']) && current_user_can('manage_options')) {
		$user_id = intval($_GET['user_id']);
		$action = sanitize_text_field($_GET['action']);

		if ($action === 'approve') {
			$user = get_userdata($user_id);
			if ($user) {
				$user->set_role('reviewed_member');
				echo '<div class="notice notice-success"><p>User approved successfully.</p></div>';
			}
		}

		if ($action === 'deny') {
			require_once(ABSPATH.'wp-admin/includes/user.php'); // Needed for wp_delete_user
			wp_delete_user($user_id);
			echo '<div class="notice notice-error"><p>User denied and deleted.</p></div>';
		}
	}

	echo '<h1>Account Review Panel</h1>';

	// Get users with the 'under_review' role
	$args = array(
		'role' => 'under_review',
		'orderby' => 'registered',
		'order' => 'ASC' // Oldest first
	);
	$under_review_users = get_users($args);

	if (empty($under_review_users)) {
		echo '<p>No users are currently under review.</p>';
		return;
	}

	echo '<table class="widefat fixed" style="max-width: 1000px;">';
	echo '<thead><tr><th>Username</th><th>Email</th><th>Registered</th><th>Actions</th></tr></thead>';
	echo '<tbody>';

	foreach ($under_review_users as $user) {
		$approve_url = admin_url('admin.php?page=account-review-panel&action=approve&user_id=' . $user->ID);
		$deny_url = admin_url('admin.php?page=account-review-panel&action=deny&user_id=' . $user->ID);

		echo '<tr>';
		echo '<td>' . esc_html($user->user_login) . '</td>';
		echo '<td>' . esc_html($user->user_email) . '</td>';
		echo '<td>' . esc_html($user->user_registered) . '</td>';
		echo '<td>';
		echo '<a href="' . esc_url($approve_url) . '" class="button button-primary" style="margin-right: 5px;">Approve</a>';
		echo '<a href="' . esc_url($deny_url) . '" class="button button-secondary" onclick="return confirm(\'Are you sure you want to delete this user?\');">Deny</a>';
		echo '</td>';
		echo '</tr>';
	}

	echo '</tbody>';
	echo '</table>';
}
?>

<?php
// Register the custom "Under Review" role on plugin activation 
function account_review_register_under_review_role() {
	add_role(
		'under_review',
		'Under Review',
		[]
	);
}
register_activation_hook(__FILE__, 'account_review_register_under_review_role');

// Automatically assign "Under Review" role to new users
function account_review_set_user_under_review($user_id) {
	$user = get_userdata($user_id);
	if ($user) {
		$user->set_role('under_review');
	}
}
add_action('user_register', 'account_review_set_user_under_review');
?>