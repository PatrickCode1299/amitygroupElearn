<?php
function lms_theme_scripts() {
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_script('main-script', get_template_directory_uri() . '/assets/js/script.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'lms_theme_scripts');

add_theme_support('title-tag');
add_theme_support('post-thumbnails');
add_filter('template_include', function ($template) {
    if (get_post_type() === 'lms_course') {
        return get_theme_file_path('custom-single-course.php');
    }
    return $template;
});
?>
