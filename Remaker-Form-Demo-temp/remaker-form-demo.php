<?php
/*
Plugin Name: Remaker Form
Description: A plugin that creates a form for remakers to register.
Version: 1.0.1
Text Domain: remaker-form-demo
Author: Ian Schaafsma
Author URI: https://www.linkedin.com/in/ian-schaafsma-2330a9270/
*/

if( !defined('ABSPATH') ) {
    echo "that isn't gonna work";
    exit;
}

class RemakerForm {

    public function __construct()
    {
        // Create custom post type
        add_action('init', array($this, 'create_custom_post_type'));

        // Add assets (js, css, etc)
        add_action('wp_enqueue_scripts', array($this, 'load_assets'));

        // Add shortcode
        add_shortcode('remaker-form', array($this, 'load_shortcode'));
    } 

    public function create_custom_post_type()
    {
        $args = array(
            'public' => true,
            'has_archive' => true,
            'supports' => array('title'),
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'capability' => 'manage_options',
            'labels' => array(
                'name' => 'Remaker Form',
                'singular_name' => 'Remaker Form Entry'
            ),
            'menu_icon' => 'dashicons-media-text',
        );

        register_post_type('remaker_form', $args);
    }

    public function load_assets()
    {
		
		if (is_page('arat-remaker-form')) {
        wp_enqueue_style(
            'remaker-form',
            plugin_dir_url( __FILE__ ) . 'css/main.css',
            array(),
            1,
            'all'
        );

        wp_enqueue_script(
            'remaker-form',
            plugin_dir_url( __FILE__ ) . 'js/main.js',
            array('jquery'),
            1,
            true // in de footer inplaats van de header
        );
		}
    }

    public function load_shortcode()
    {
        return '<script defer>
        
        window.addEventListener("load", (event) => {
        renderCard(current);
        });
        
        </script>';
    }

}

new RemakerForm;

// Shortcode om index.html in te laden
