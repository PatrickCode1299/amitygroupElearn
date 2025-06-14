<?php /* Template Name: View Course */ get_header(); ?>
<?php
$user_id = get_current_user_id();
$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$is_enrolled = true; // Replace with actual enrollment check
if (!$is_enrolled) {
    echo '<p>You are not enrolled in this course.</p>';
    get_footer(); exit;
}
?>
<h2>Course Content</h2>
<?php echo do_shortcode('[gdlr_core_course_content id="' . $course_id . '"]'); ?>
<?php get_footer(); ?>
