<?php 
    add_action('template_redirect', function () {
        if (is_user_logged_in()) return;

        $current_url = $_SERVER['REQUEST_URI'];
        $excluded_paths = [
            '/arat-eco-page/',
            '/login/',
            '/register-2/',
            '/wp-login.php'
        ];

        foreach ($excluded_paths as $excluded) {
            if (stripos($current_url, $excluded) !== false) return;
        }

        wp_redirect(home_url('/arat-eco-page/'));
        exit;
    });

    add_filter('login_redirect', function ($redirect_to, $request, $user) {
        if (is_wp_error($user) || !is_object($user)) return $redirect_to;
        return home_url('/');
    }, 10, 3);

    add_filter('peepso_register_redirect_url', function () {
        return home_url('/login/');
    });
?>

