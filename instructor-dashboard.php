<?php
/* Template Name: Instructor Dashboard */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$user = wp_get_current_user();
if (!in_array('instructor', $user->roles)) {
    echo '<p style="color:red; text-align:center;">Access denied. Your instructor account is not yet approved.</p>';
    get_footer();
    exit;
}

?>

<div style="max-width: 900px; margin: 2rem auto; padding: 2rem; background:#f0f8ff; border-radius:8px;">
<h2 style="color:#004aad;">Welcome, <?php echo esc_html($user->display_name); ?></h2>

<h3>Your Courses & Enrollments</h3>

<?php
// Query instructor courses
$courses = get_posts([
    'post_type' => 'course',
    'author' => $user->ID,
    'posts_per_page' => -1,
]);

if (!$courses) {
    echo "<p>No courses found.</p>";
} else {
    echo '<table style="width:100%; border-collapse:collapse;">';
    echo '<thead style="background:#cce0ff; color:#004aad;"><tr><th style="border:1px solid #ddd; padding:8px;">Course Title</th><th style="border:1px solid #ddd; padding:8px;">Status</th><th style="border:1px solid #ddd; padding:8px;">Enrollments</th></tr></thead>';
    echo '<tbody>';
    foreach ($courses as $course) {
        // Count enrolled users for this course (assumes enrolment tracked, example below)
        $enrollment_count = intval(get_post_meta($course->ID, 'enrollment_count', true)) ?: 0;

        echo '<tr>';
        echo '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($course->post_title) . '</td>';
        echo '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($course->post_status) . '</td>';
        echo '<td style="border:1px solid #ddd; padding:8px;">' . $enrollment_count . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>

</div>

<?php get_footer(); ?>
