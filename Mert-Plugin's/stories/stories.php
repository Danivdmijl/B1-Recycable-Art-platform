<?php
/**
 * Plugin Name: Stories
 * Description: A simple WordPress story plugin that lets users post photo stories which disappear after 2 minutes.
 * Version: 1.0
 * Author: Mert Korkmaz
 */

add_action('wp_enqueue_scripts', 'stories_enqueue_styles');
function stories_enqueue_styles() {
    wp_enqueue_style(
        'stories-style',
        plugin_dir_url(__FILE__) . 'css/stories.css',
        [],
        '1.3'
    );
}

register_activation_hook(__FILE__, 'stories_activate');
register_deactivation_hook(__FILE__, 'stories_deactivate');

function stories_activate() {
    if (!wp_next_scheduled('stories_cleanup_event')) {
        wp_schedule_event(time(), 'every_two_minutes', 'stories_cleanup_event');
    }
}

function stories_deactivate() {
    wp_clear_scheduled_hook('stories_cleanup_event');
}

add_filter('cron_schedules', 'stories_custom_schedule');
function stories_custom_schedule($schedules) {
    $schedules['every_two_minutes'] = [
        'interval' => 120,
        'display' => __('Every 2 Minutes')
    ];
    return $schedules;
}

add_action('init', 'stories_post_type');
function stories_post_type() {
    register_post_type('story', [
        'public' => false,
        'show_ui' => true,
        'label' => 'Stories',
        'supports' => ['author', 'thumbnail'],
    ]);
}

add_action('stories_cleanup_event', 'stories_cleanup');
function stories_cleanup() {
    $args = [
        'post_type' => 'story',
        'posts_per_page' => -1
    ];
    $stories = get_posts($args);
    foreach ($stories as $story) {
        $post_time = strtotime($story->post_date_gmt);
        if (time() - $post_time >= 120) {
            wp_delete_post($story->ID, true);
        }
    }
}

add_shortcode('story_upload', 'story_upload_form');
function story_upload_form() {
    if (!is_user_logged_in()) {
        return '<p>You must be logged in to post a story.</p>';
    }

    ob_start();
    ?>
    <form action="" method="post" enctype="multipart/form-data">
        <p>
            <label for="story_name">Your name</label><br>
            <input type="text" name="story_name" id="story_name" required>
        </p>

        <p>
            <input type="file" name="story_media" id="story_media" accept="image/*" required hidden>
            <button type="button" class="custom-upload-btn" onclick="document.getElementById('story_media').click()">Choose a photo</button>
        </p>

        <p>
            <input type="submit" name="submit_story" value="Post story">
        </p>
        <?php 
            if (isset($_GET['story']) && $_GET['story'] === 'posted') {
                echo '<div class="story-success" id="storyMessage">✅ Story posted!</div>';
            }
        ?>
    </form>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const msg = document.getElementById("storyMessage");
        if (msg) {
            setTimeout(() => {
                msg.style.display = "none";
            }, 5000);
        }
    });
    </script>
    <?php
    return ob_get_clean();
}

add_action('init', 'handle_story_upload');
function handle_story_upload() {
    if (isset($_POST['submit_story']) && is_user_logged_in()) {
        $name = sanitize_text_field($_POST['story_name']);
        $user_id = get_current_user_id();

        $post_id = wp_insert_post([
            'post_title' => 'Story',
            'post_type' => 'story',
            'post_status' => 'publish',
            'post_author' => $user_id,
        ]);

        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }

        $attachment_id = media_handle_upload('story_media', $post_id);
        if (!is_wp_error($attachment_id)) {
            set_post_thumbnail($post_id, $attachment_id);
        }

        if ($name) {
            update_post_meta($post_id, '_story_name', $name);
        }

        wp_redirect(add_query_arg('story', 'posted', remove_query_arg('submit_story', wp_get_referer())));
        exit;
    }
}

add_shortcode('stories', 'display_stories');
function display_stories() {
    ob_start();
    $args = [
        'post_type' => 'story',
        'posts_per_page' => 20,
        'orderby' => 'date',
        'order' => 'DESC',
        'date_query' => [
            ['column' => 'post_date', 'after' => '2 minutes ago']
        ]
    ];
    $stories = get_posts($args);

    echo '<div class="stories">';
    foreach ($stories as $story) {
        $story_id = $story->ID;
        $thumbnail = get_the_post_thumbnail_url($story_id, 'medium');
        $attachment_id = get_post_thumbnail_id($story_id);
        $mime_type = get_post_mime_type($attachment_id);
        $full_media_url = wp_get_attachment_url($attachment_id);
        $name = get_post_meta($story_id, '_story_name', true);

        echo "<div class='story-item'>";
        echo "<div class='story-circle' data-id='{$story_id}' data-url='{$full_media_url}' data-type='{$mime_type}'>";
        echo "<img src='{$thumbnail}' alt='story'>";
        echo "</div>";
        if ($name) {
            echo "<p class='story-name'>{$name}</p>";
        }
        echo "</div>";
    }
    echo '</div>';

    echo '
    <div class="story-modal" id="storyModal" onclick="closeStoryModal()">
        <div id="storyContent"></div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const circles = document.querySelectorAll(".story-circle");
        circles.forEach(el => {
            const id = el.dataset.id;
            if (localStorage.getItem("viewed_story_" + id)) {
                el.classList.add("viewed");
            }

            el.addEventListener("click", function () {
                openStoryModal(el.dataset.url, el.dataset.type);
                el.classList.add("viewed");
                localStorage.setItem("viewed_story_" + id, "true");
            });
        });
    });

    function openStoryModal(mediaUrl, type) {
        const modal = document.getElementById("storyModal");
        const content = document.getElementById("storyContent");
        content.innerHTML = "";

        const img = document.createElement("img");
        img.src = mediaUrl;
        content.appendChild(img);

        modal.classList.add("active");

        setTimeout(() => {
            closeStoryModal();
        }, 5000);
    }

    function closeStoryModal() {
        const modal = document.getElementById("storyModal");
        modal.classList.remove("active");
    }
    </script>
    ';

    return ob_get_clean();
}
