<?php
/*
Template Name: Paywall
*/
get_header();

$course_id = intval($_GET['course_id'] ?? 0);
$course = $course_id ? get_post($course_id) : null;
if (!$course) {
    echo '<p>Invalid course.</p>';
    get_footer(); exit;
}

// Handle payment submission here (Stripe, Paystack, etc.)
// On success:
if (/* payment succeeded */) {
    gdlr_lms_grant_course_access($course_id, get_current_user_id());
    wp_redirect(get_permalink($course_id));
    exit;
}
?>

<style>
.paywall { max-width: 600px; margin:3rem auto; background:#fff; padding:2rem; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1); }
.paywall h2 { margin-bottom:1.5rem; color:#004aad; }
.paywall button { background:#0073aa; color:#fff; padding:.75rem 1.5rem; border:none; border-radius:4px; cursor:pointer; }
.paywall button:hover { background:#005e8a; }
</style>

<div class="paywall">
    <h2><?= esc_html($course->post_title) ?></h2>
    <p>Price: $<?= number_format((float)get_post_meta($course_id, 'gdlr-lms-course-price', true), 2) ?></p>
    <form method="post">
        <!-- Payment gateway form -->
        <button type="submit">Pay Now</button>
    </form>
</div>

<?php get_footer(); ?>
