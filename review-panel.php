	<?php
	/**
	 * Plugin Name: Account Review Panel
	 * Description: This plugin allows administrators to manually review and approve or deny new user registrations via a custom admin panel. Users under review are blocked from logging in until 		approved. Includes custom roles, easy test account generator and remover and a styled UI with registration info of the users.
	 * Version: 2.0
	 * Author: Dani van der Mijl
	 * Author URI: https://www.linkedin.com/in/dani-van-der-mijl/
	 * License: GPL2
	 */
	?>

	<?php
	// Add a menu item for the plugin in the wordpress menu
	function account_review_add_admin_menu() {
		add_menu_page(
			'Account Review Panel',   
			'Account Review',        
			'manage_options',         
			'account-review-panel',   
			'account_review_settings_page' 
		);
	}

	function account_review_custom_menu_style() {
		echo '
		<style>
			#adminmenu .toplevel_page_account-review-panel > a {
				background: #55B76B !important; /* Accent green */
				color: #ffffff !important;
				font-weight: bold;
			}

			#adminmenu .toplevel_page_account-review-panel:hover > a {
				background: #00A6ED !important; /* Blue accent */
				color: #ffffff !important;
			}

			#adminmenu .toplevel_page_account-review-panel:hover div.wp-menu-image:before {
				color: #ffffff !important;
			}

			.widefat tfoot tr td, .widefat tfoot tr th, .widefat thead tr td, .widefat thead tr th {
				color: white;
				font-weight: 500;
			}

			.aratthead {
				background: #4B9C61;
			}
			.aratthead th {
				position: sticky;
				top: 0;
				background: #4B9C61;
				z-index: 10;
				box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);

			}
			.aratcenter {
				text-align: center !important;
				vertical-align: middle !important;
				font-weight: 500 !important;
			}
			body:not(#__) .aratbuttonapprove {
				background: #01A7ED;
				border: 1px solid #01A7ED;
				color: white;
			}
			body:not(#__) .aratbuttonapprove:hover {
				background: #4B9C61;
				border: 1px solid #4B9C61;
				transition: all 0.25s ease-in-out;
				color: white;
				transform: scale(1.07);f
			}

			body:not(#__) .aratbuttondeny:hover {
				background: #C6131B;
				border: 1px solid #C6131B;
				transition: all 0.25s ease-in-out;	
				color: white;
				transform: scale(1.1);
			}
			.aratmascot-wrapper {
			    position: fixed;
				bottom: 0;
				right: 0;
				z-index: 9999;
				pointer-events: none;				
			}
			.aratmascot-wrapper img.aratmascot {
				height: 200px;
				width: auto;
			}
			.aratmascot-wrapper img.aratmascot {
				height: 400px;
				width: auto;
			}
			.arattitle {
				font-size: 26px;
				margin-top: 50px;
				margin-bottom: 30px;
			}

			/* Custom Scrollbar Styling */
	::-webkit-scrollbar {
		width: 13px;
	}

	::-webkit-scrollbar-track {
		background: #f0f0f0;
		border-radius: 5px;
	}

	::-webkit-scrollbar-thumb {
		background-color: #4B9C61;
		border-radius: 5px;
		border: 2px solid #f0f0f0;
	}


	::-webkit-scrollbar-thumb:hover {
		cursor: grab;
	}

	scrollbar-width: auto;
	scrollbar-color: #4B9C61 #f0f0f0;

		</style>';
	}

	add_action('admin_head', 'account_review_custom_menu_style');

	add_action('admin_menu', 'account_review_add_admin_menu');

	function account_review_enqueue_admin_styles($hook) {
		if ($hook !== 'toplevel_page_account-review-panel') {
			return;
		}
		wp_enqueue_style('account-review-admin-style', plugin_dir_url(__FILE__) . 'admin-style.css');
	}
	add_action('admin_enqueue_scripts', 'account_review_enqueue_admin_styles');

	function account_review_disable_scroll() {
	$screen = get_current_screen();
	if ($screen->id === 'toplevel_page_account-review-panel') {
		echo '
		<style>
			html, body {
				overflow: hidden !important;
			}
		</style>';
	}
}
add_action('admin_head', 'account_review_disable_scroll');


	// Create the settings page content
	function account_review_settings_page() {
		// Handle Approve or Deny actions
		if (isset($_GET['action']) && isset($_GET['user_id']) && current_user_can('manage_options')) {
			$user_id = intval($_GET['user_id']);
			$action = sanitize_text_field($_GET['action']);
if ($action === 'approve') {
    $user = get_userdata($user_id);
    if ($user) {
        // Check if the user has the "under_review_remaker" role
        if (in_array('under_review_remaker', (array) $user->roles)) {
            // Change role to "Reviewed Remaker" if user is an under_review_remaker
            $user->set_role('reviewed_remaker');
        } elseif (in_array('under_review', (array) $user->roles)) {
            // Change role to "Reviewed Member" if user is under review
            $user->set_role('reviewed_member');
        }
        echo '<div class="notice notice-success"><p>User approved successfully.</p></div>';
    }

			}
			if ($action === 'deny') {
				require_once(ABSPATH.'wp-admin/includes/user.php');
				wp_delete_user($user_id);
				echo '<div class="notice notice-error"><p>User denied and deleted.</p></div>';
			}
		}
		
		echo '<div class="aratmascot-wrapper">
	<img src="https://arat-wp.duckduckdev.nl/wp-content/uploads/2025/05/Asset-4-1-scaled.png" class="aratmascot" alt="Mascot" />
</div>';

		echo '<h1 class="arattitle">Account Review Panel</h1>';

		// Create fake users
		if (isset($_GET['create_fake']) && current_user_can('manage_options')) {
			account_review_create_fake_users();
			echo '<div class="notice notice-success"><p>40 fake users created</p></div>';
		}

		// Delete test users
		if (isset($_GET['delete_test_users']) && current_user_can('manage_options')) {
			account_review_delete_test_users();
			echo '<div class="notice notice-error"><p>All testuser accounts deleted</p></div>';
		}

		echo '<p>
			<a href="' . admin_url('admin.php?page=account-review-panel&create_fake=true') . '" class="button button-secondary">Create 40 Test Accounts</a>
			<a href="' . admin_url('admin.php?page=account-review-panel&delete_test_users=true') . '" class="button button-secondary" style="margin-left: 10px;" onclick="return confirm(\'Are you sure you want to delete all testuser accounts?\');">Delete Test Accounts</a>
		</p>';

		// Get users with the 'under_review' or 'under_review_remaker' role
		$args = array(
			'role__in' => array('under_review', 'under_review_remaker'), // Picking the users with either role
			'orderby' => 'registered',
			'order' => 'ASC' // Oldest first
		);
		$under_review_users = get_users($args);

		if (empty($under_review_users)) {
			echo '<p>No users are currently under review.</p>';
			return;
		}

// 		echo '<div class="aratmascot-wrapper"><img src="https://arat-wp.duckduckdev.nl/wp-content/uploads/2025/05/Asset-4-1-scaled.png" class="aratmascot" alt="Mascot" /></div>';
		echo '<div style="max-height: 600px; overflow-y: auto; max-width: 1000px; margin-bottom: 20px;">';
		echo '<table class="widefat fixed" style="width: 100%;">';
		echo '</div>';
		echo '<thead class="aratthead">';
		echo '<tr>';
		echo '<th class="aratcenter">Profile Picture</th>';
		echo '<th class="aratcenter">Username</th>';
		echo '<th class="aratcenter">Email</th>';
		echo '<th class="aratcenter">Registered</th>';
		echo '<th class="aratcenter">Registered as</th>';
		echo '<th class="aratcenter">Actions</th>';
		echo '</tr>';
		echo '</thead>';
		echo '<tbody>';

		foreach ($under_review_users as $user) {
			$approve_url = admin_url('admin.php?page=account-review-panel&action=approve&user_id=' . $user->ID);
			$deny_url = admin_url('admin.php?page=account-review-panel&action=deny&user_id=' . $user->ID);
			$avatar = get_avatar($user->ID, 48); // 48px breed

			echo '<tr>';
			echo '<td class="aratcenter">' . $avatar . '</td>';
			echo '<td class="aratcenter">' . esc_html($user->user_login) . '</td>';
			echo '<td class="aratcenter">' . esc_html($user->user_email) . '</td>';
			echo '<td class="aratcenter">' . esc_html($user->user_registered) . '</td>';
			echo '<td class="aratcenter">Coming Soon</td>';
			echo '<td class="aratcenter">';
			echo '<a href="' . esc_url($approve_url) . '" class="button button-primary aratbuttonapprove" style="margin-right: 5px;">Approve</a>';
			echo '<a href="' . esc_url($deny_url) . '" class="button button-secondary aratbuttondeny" onclick="return confirm(\'Are you sure you want to delete this user?\');">Deny</a>';
			echo '</td>';
			echo '</tr>';
		}

		echo '</tbody>';
		echo '</table>';
	}

	?>

	<?php
	// Register the custom "Under Review" role on plugin activation 

	function account_review_register_reviewed_member_role() {
		add_role(
			'reviewed_member',
			'Reviewed Member',
			[] 
		);
	}

	function account_underreview_register_remaker_role() {
		add_role(
			'under_review_remaker',
			'Under Review Remaker', 
			[] 
		);
	}

	function account_review_register_reviewed_remaker_role() {
		add_role(
			'reviewed_remaker', 
			'Reviewed Remaker', 
			[]
		);
	}

	function account_review_register_under_review_role() {
		add_role(
			'under_review',
			'Under Review',
			[]
		);
	}

	function account_review_activate_plugin() {
		account_review_register_under_review_role();
		account_review_register_reviewed_member_role();
		account_underreview_register_remaker_role();
		account_review_register_reviewed_remaker_role();
	}
	register_activation_hook(__FILE__, 'account_review_activate_plugin');
	?>


	<?php
	function account_review_block_under_review_login($user, $username, $password) {
		if (is_wp_error($user)) {
			return $user;
		}

		// Check if the user has the "under_review" role
		if (in_array('under_review', (array) $user->roles)) {
			return new WP_Error(
				'under_review_blocked',
				__('Your account is currently under review and cannot log in yet.')
			);
		}

		return $user;
	}
	add_filter('authenticate', 'account_review_block_under_review_login', 30, 3);

	function enqueue_review_panel_styles() {
		wp_enqueue_style('review-panel-style', plugin_dir_url(__FILE__) . 'css/review-panel.css');
	}
	add_action('admin_enqueue_scripts', 'enqueue_review_panel_styles');

	function account_review_create_fake_users() {
		if (!current_user_can('manage_options')) return;

		$existing = get_users(['role' => 'under_review']);
		if (count($existing) >= 40) return; // Prevent flooding

		for ($i = 1; $i <= 40; $i++) {
			$username = 'testuser' . $i;
			if (!username_exists($username)) {
				$user_id = wp_create_user($username, 'TestPassword123!', $username . '@example.com');
				if (!is_wp_error($user_id)) {
					$user = new WP_User($user_id);
					$user->set_role('under_review');
				}
			}
		}
	}

	function account_review_delete_test_users() {
		if (!current_user_can('manage_options')) return;

		$users = get_users([
			'search' => 'testuser*',
			'search_columns' => ['user_login'],
		]);

		require_once(ABSPATH.'wp-admin/includes/user.php'); // Voor wp_delete_user

		foreach ($users as $user) {
			wp_delete_user($user->ID);
		}
	}

// Zet automatisch de rol 'under_review' bij nieuwe gebruikers
function account_review_set_user_under_review($user_id) {
    $user = new WP_User($user_id);

    // Alleen wijzigen als het een standaard gebruiker is (bijv. 'subscriber')
    if (in_array('subscriber', $user->roles)) {
        $user->set_role('under_review');
    }
}
add_action('user_register', 'account_review_set_user_under_review', 10, 1);


