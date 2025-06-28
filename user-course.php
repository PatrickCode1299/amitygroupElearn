<?php
/**
 * Template Name: User Courses Dashboard
 */

get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user_id = get_current_user_id();
$recent_course_id = get_user_meta($current_user_id, 'gdlrms_last_viewed_course', true);
$enrolled_courses = get_user_meta($current_user_id, 'gdlrms_enrolled_courses', true);
if (!is_array($enrolled_courses)) $enrolled_courses = [];

function get_course_progress($user_id, $course_id) {
    $progress = get_user_meta($user_id, 'gdlrms_course_progress_' . $course_id, true);
    return ($progress !== '') ? intval($progress) : 0;
}
?>

<style>
body {
  background: #f8fbff;
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
}
.dashboard-container {
  display: flex;
  min-height: 100vh;
  flex-wrap: wrap;
}
.sidebar, .right-panel {
  background: #ffffff;
  padding: 2rem 1rem;
  flex-shrink: 0;
}
.sidebar {
  width: 230px;
  border-right: 1px solid #e0e0e0;
}
.logo {
  font-size: 1.5rem;
  font-weight: bold;
  color: #0074ff;
  margin-bottom: 2rem;
}
.join-course-btn {
  background: #0074ff;
  color: white;
  padding: 0.6rem;
  border: none;
  border-radius: 10px;
  margin-bottom: 2rem;
  cursor: pointer;
  width: 100%;
}
.nav-links a {
  display: block;
  padding: 0.7rem 1rem;
  margin-bottom: 0.5rem;
  color: #333;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.2s ease;
}
.nav-links a.active, .nav-links a:hover {
  background: #e6f0ff;
  color: #0074ff;
}
.upgrade-card {
  margin-top: auto;
  background: #e8f3ff;
  padding: 1rem;
  border-radius: 10px;
  text-align: center;
}
.upgrade-btn {
  margin-top: 0.5rem;
  background: #004aad;
  color: white;
  border: none;
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
}
.main-content {
  flex: 1;
  padding: 2rem;
  background: linear-gradient(to bottom right, #f0f7ff, #ffffff);
}
.main-content h2 {
  margin-bottom: 1.5rem;
  font-weight: 700;
  font-size: 2rem;
  color: #004aad;
  border-bottom: 3px solid #0074ff;
  padding-bottom: 0.5rem;
}
.recent-course-card {
  background: #e6f0ff;
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 2px 8px rgb(0 74 173 / 0.1);
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 3rem;
}
.recent-course-card img {
  width: 130px;
  height: 90px;
  border-radius: 8px;
  object-fit: cover;
}
.recent-course-info h3 {
  margin: 0;
  color: #004aad;
  font-size: 1.3rem;
}
.recent-course-info p {
  margin: 0.3rem 0 0;
  color: #0050ff;
  font-weight: 600;
}
.recent-course-info a {
  margin-top: 0.8rem;
  display: inline-block;
  background: #0074ff;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 700;
  transition: background 0.3s ease;
}
.recent-course-info a:hover {
  background: #004aad;
}
.courses-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit,minmax(280px,1fr));
  gap: 1.5rem;
}
.course-card {
  background: #f0f7ff;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgb(0 74 173 / 0.15);
  padding: 1.5rem;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}
.course-card:hover {
  box-shadow: 0 6px 16px rgb(0 74 173 / 0.3);
}
.course-card img {
  width: 100%;
  border-radius: 8px;
  margin-bottom: 1rem;
  object-fit: cover;
  height: 140px;
}
.course-card h3 {
  font-size: 1.2rem;
  color: #004aad;
  margin-bottom: 0.5rem;
}
.course-card .progress-bar-container {
  background: #dbe9ff;
  border-radius: 20px;
  height: 12px;
  margin-top: 1rem;
  overflow: hidden;
}
.course-card .progress-bar {
  background: #0074ff;
  height: 100%;
  width: 0%;
  transition: width 0.6s ease;
  border-radius: 20px 0 0 20px;
}
.course-card .progress-text {
  font-weight: 600;
  margin-top: 0.4rem;
  color: #004aad;
  text-align: right;
  font-size: 0.9rem;
}
.right-panel {
  width: 280px;
  border-left: 1px solid #e0e0e0;
}
.user-info {
  text-align: center;
  margin-bottom: 2rem;
}
.user-info .avatar {
  width: 60px;
  border-radius: 50%;
}
.user-info h4 {
  margin-top: 0.6rem;
  font-size: 1.1rem;
  color: #004aad;
}
.progress-summary h5,
.upcoming-tasks h5,
.download-app h5 {
  font-size: 1rem;
  color: #333;
  margin-bottom: 0.8rem;
}
.progress-summary ul,
.upcoming-tasks ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.progress-summary li {
  margin-bottom: 1rem;
}
.progress-summary .course-name {
  font-size: 0.9rem;
  font-weight: 600;
  color: #004aad;
}
.progress-bar-sm {
  background: #dbe9ff;
  height: 8px;
  border-radius: 6px;
  overflow: hidden;
  margin-top: 0.2rem;
}
.progress-bar-sm div {
  height: 100%;
  background: #0074ff;
  border-radius: 6px;
}
.upcoming-tasks li {
  margin-bottom: 1rem;
  font-size: 0.9rem;
  color: #555;
}
.store-links img {
  margin-right: 10px;
  cursor: pointer;
  height: 24px;
}

/* Responsive Layout */
@media screen and (max-width: 992px) {
  .sidebar,
  .right-panel {
    display: none;
  }
  .main-content {
    padding: 1.5rem 1rem;
    width: 100%;
  }
  .recent-course-card {
    flex-direction: column;
    align-items: flex-start;
  }
  .recent-course-card img {
    width: 100%;
    height: auto;
  }
}
</style>

<div class="dashboard-container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div>
      <a href="<?php echo site_url('/'); ?>" class="join-course-btn">+ Join a Course</a>
      <nav class="nav-links">
        <a href="#">Dashboard</a>
        <a href="#" class="active">All Courses</a>
        <a href="#">Resources</a>
        <a href="#">Friends</a>
        <a href="#">Chats</a>
        <a href="#">Settings</a>
      </nav>
    </div>
  </aside>

  <!-- Main Content -->
  <main class="main-content">
    <?php if ($recent_course_id && get_post_status($recent_course_id) === 'publish') :
      $recent_course_title = get_the_title($recent_course_id);
      $recent_course_link = get_permalink($recent_course_id);
      $recent_course_thumbnail = get_the_post_thumbnail_url($recent_course_id, 'medium');
      $recent_course_progress = get_course_progress($current_user_id, $recent_course_id);
    ?>
      <h2>Recently Viewed Course</h2>
      <div class="recent-course-card">
        <img src="<?php echo esc_url($recent_course_thumbnail ?: get_template_directory_uri() . '/assets/default-course.png'); ?>" alt="<?php echo esc_attr($recent_course_title); ?>">
        <div class="recent-course-info">
          <h3><?php echo esc_html($recent_course_title); ?></h3>
          <p>Progress: <?php echo intval($recent_course_progress); ?>%</p>
          <a href="<?php echo esc_url($recent_course_link); ?>">Continue Course</a>
        </div>
      </div>
    <?php endif; ?>

    <h2>Your Enrolled Courses</h2>
    <?php if (empty($enrolled_courses)) : ?>
      <p>You are not enrolled in any courses yet.</p>
    <?php else : ?>
      <div class="courses-grid">
        <?php foreach ($enrolled_courses as $course_id) :
          if (get_post_status($course_id) !== 'publish') continue;
          $course_title = get_the_title($course_id);
          $course_slug = get_post_field('post_name', $course_id);
          $course_link = site_url('/classroom/' . $course_slug);
          $course_thumbnail = get_the_post_thumbnail_url($course_id, 'medium');
          $progress = get_course_progress($current_user_id, $course_id);
        ?>
          <a href="<?php echo esc_url($course_link); ?>" class="course-card">
            <img src="<?php echo esc_url($course_thumbnail ?: get_template_directory_uri() . '/assets/default-course.png'); ?>" alt="<?php echo esc_attr($course_title); ?>">
            <h3><?php echo esc_html($course_title); ?></h3>
            <div class="progress-bar-container">
              <div class="progress-bar" style="width: <?php echo $progress; ?>%;"></div>
            </div>
            <div class="progress-text"><?php echo $progress; ?>% Completed</div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </main>

  <!-- Right Panel -->
  <aside class="right-panel">
    <div class="user-info">
      <img src="https://ui-avatars.com/api/?name=<?php echo urlencode(wp_get_current_user()->display_name); ?>&background=0074ff&color=fff" class="avatar" alt="User">
      <h4><?php echo esc_html(wp_get_current_user()->display_name); ?></h4>
    </div>

    <div class="progress-summary">
      <h5>Progress</h5>
      <ul>
        <?php foreach ($enrolled_courses as $course_id) :
          $title = get_the_title($course_id);
          $progress = get_course_progress($current_user_id, $course_id);
        ?>
          <li>
            <span class="course-name"><?php echo esc_html($title); ?></span>
            <div class="progress-bar-sm"><div style="width:<?php echo $progress; ?>%"></div></div>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>

 
  </aside>
</div>


