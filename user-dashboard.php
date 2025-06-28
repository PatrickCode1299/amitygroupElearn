<?php
/* Template Name: User Dashboard */

$user_course_ids = get_user_meta($current_user->ID, 'gdlr-lms-enrolled-course', true);

if (!is_array($user_course_ids)) {
    $user_course_ids = []; // Ensure it's always an array
}
get_header();
$user_courses = !empty($user_course_ids) ? get_posts([
    'post_type' => 'course',
    'post__in' => $user_course_ids,
    'numberposts' => -1
]) : [];

?>

<style>
.dashboard-wrapper {
    padding: 2rem;
    max-width: 1200px;
    margin: auto;
    font-family: 'Segoe UI', sans-serif;
    color: #1a1a1a;
}
.dashboard-header {
    background: #004aad;
    color: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
}
.dashboard-header h1 {
    margin: 0;
    font-size: 2.5rem;
}
.dashboard-content {
    margin-top: 2rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.card {
    background: white;
    border-radius: 10px;
    padding: 1.5rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
.card i {
    font-size: 2rem;
    color: #004aad;
}
.card h3 {
    margin: 1rem 0 0.5rem;
    font-size: 1.3rem;
}
.card p {
    margin: 0;
    font-size: 0.95rem;
    color: #555;
}
@media (max-width: 600px) {
    .dashboard-header h1 {
        font-size: 1.8rem;
    }
}
</style>

<div class="dashboard-wrapper">
    <div class="dashboard-header">
        <h1>Welcome, <?= esc_html($current_user->display_name); ?> 👋</h1>
        <p>Here’s your learning dashboard</p>
    </div>

    <div class="dashboard-content">
      <a class="card" href="<?php echo site_url('/my-courses'); ?>">  
            <i class="fas fa-book-open"></i>
            <h3>My Courses</h3>
            <p>You are enrolled in <?= count($user_courses); ?> course(s)</p>
</a>

         <a class="card" href="<?php echo site_url('/my-courses'); ?>">  
            <i class="fas fa-chart-line"></i>
            <h3>Progress</h3>
            <p>Check your progress and continue where you left off</p>
</a>

        <div class="card">
            <i class="fas fa-certificate"></i>
            <h3>Certificates</h3>
            <p>View and download earned certificates</p>
        </div>

        <div class="card">
            <i class="fas fa-user-cog"></i>
            <h3>Account Settings</h3>
            <p>Update your profile, email and password</p>
        </div>
    </div>
</div>

