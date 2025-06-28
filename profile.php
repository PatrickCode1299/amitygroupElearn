<?php
/* Template Name: Student Profile */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(site_url('/login'));
    exit;
}

$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$enrolled_courses = get_user_meta($user_id, 'gdlr-lms-enrolled-course', true);
$courses = is_array($enrolled_courses) ? $enrolled_courses : [];
?>

<!-- Font Awesome -->
<link href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css" rel="stylesheet">

<style>
body {
    background: #f0f4f8;
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding:0;
}
.dashboard-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
}
.dashboard-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #ddd;
    padding-bottom: 1rem;
    margin-bottom: 2rem;
}
.dashboard-header h1 {
    font-size: 2rem;
    color: #004aad;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.dashboard-header a {
    color: #ff4b4b;
    text-decoration: none;
    font-weight: bold;
}
.profile-card {
    background: #eaf3ff;
    padding: 1.5rem;
    border-radius: 10px;
    display: flex;
    gap: 1.5rem;
    align-items: center;
    margin-bottom: 2rem;
}
.profile-card img {
    border-radius: 50%;
    width: 90px;
    height: 90px;
}
.profile-details {
    flex: 1;
}
.profile-details h2 {
    margin: 0;
    font-size: 1.5rem;
    color: #004aad;
}
.profile-details p {
    margin: 0.25rem 0;
    color: #555;
}
.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}
.card {
    background: #f8fafd;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    transition: all 0.3s ease;
}
.card:hover {
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.card h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
    color: #004aad;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.card ul {
    list-style: none;
    padding: 0;
}
.card ul li {
    margin-bottom: 0.75rem;
    background: #fff;
    padding: 0.75rem;
    border-radius: 6px;
    border-left: 4px solid #004aad;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.card ul li a {
    text-decoration: none;
    color: #004aad;
    font-weight: 500;
}
.card ul li i {
    color: #004aad;
}
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="dashboard-container">
    <div class="dashboard-header">
        <h1><i class="fas fa-gauge-high"></i> Student Profile </h1>
    </div>

    <div class="profile-card">
        <?php echo get_avatar($user_id, 90); ?>
        <div class="profile-details">
            <h2><?php echo esc_html($current_user->display_name); ?></h2>
            <p><i class="fas fa-envelope"></i> <?php echo esc_html($current_user->user_email); ?></p>
            <p><i class="fas fa-user"></i> <?php echo esc_html($current_user->user_login); ?></p>
        </div>
    </div>

    <div class="card-grid">

        <div class="card">
            <h3><i class="fas fa-chart-line"></i> Performance</h3>
            <ul>
                <li><i class="fas fa-book-check"></i> Completed Courses: <strong>0</strong></li>
                <li><i class="fas fa-bars-progress"></i> Progress: <strong>Coming soon...</strong></li>
                <li><i class="fas fa-award"></i> Certifications: <strong>N/A</strong></li>
            </ul>
        </div>

        <div class="card">
            <h3><i class="fas fa-clock-rotate-left"></i> Profile History</h3>
            <ul>
                <li><i class="fas fa-calendar-check"></i> Joined: <strong><?php echo date('F j, Y', strtotime($current_user->user_registered)); ?></strong></li>
                <li><i class="fas fa-user-clock"></i> Last Login: <strong>N/A</strong></li>
                <li><i class="fas fa-user-tag"></i> Role: <strong><?php echo implode(', ', $current_user->roles); ?></strong></li>
            </ul>
        </div>

        <div class="card">
            <h3><i class="fas fa-graduation-cap"></i> Enrolled Courses</h3>
            <ul>
                <?php if (!empty($courses)):
                    foreach ($courses as $course_id): ?>
                        <li><i class="fas fa-play-circle"></i> <a href="<?php echo get_permalink($course_id); ?>"><?php echo get_the_title($course_id); ?></a></li>
                    <?php endforeach;
                else: ?>
                    <li><i class="fas fa-info-circle"></i> You have not enrolled in any courses yet.</li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</div>


