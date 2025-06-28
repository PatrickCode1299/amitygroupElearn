<?php
get_header();

// Fix: Set global $post from course slug query var
$course_slug = get_query_var('custom_course_slug');

if ($course_slug) {
    $course_post = get_page_by_path($course_slug, OBJECT, 'course');
    if ($course_post) {
        global $post;
        $post = $course_post;
        setup_postdata($post);
        $course_id = $post->ID;
    } else {
        echo '<h2>Course not found.</h2>';
        get_footer();
        exit;
    }
} else {
    echo '<h2>No course specified.</h2>';
    get_footer();
    exit;
}

// Check login
if ( ! is_user_logged_in() ) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();

if ( ! $course_id ) {
    echo '<p>Invalid course.</p>';
    get_footer();
    exit;
}

// Get course content settings (modules)
$settings = gdlr_lms_get_course_content_settings($course_id);
$modules = [];

foreach ($settings as $section) {
    $lectures = json_decode($section['lecture-section'], true);
    if (!is_array($lectures)) continue;

    foreach ($lectures as $lec) {
        $video_url = '';

        // Extract video URL from lecture-content using regex
        if (!empty($lec['lecture-content'])) {
            if (preg_match('/https?:\/\/[^"\']+\.(mp4|mov|webm|ogg|m4v|avi|pdf)/i', $lec['lecture-content'], $matches)) {
                $video_url = esc_url($matches[0]);
            }
        }

        // Fallback to lecture-url if no video found
        if (empty($video_url) && !empty($lec['lecture-url'])) {
            $video_url = esc_url($lec['lecture-url']);
        }

        $modules[] = [
            'id' => isset($lec['lecture-title']) ? sanitize_title($lec['lecture-title']) : 'untitled-' . uniqid(),
            'title' => isset($section['section-name']) ? $section['section-name'] : 'Untitled Lecture',
            'url' => $video_url,
            'thumbnail' => isset($lec['lecture-thumbnail']) ? $lec['lecture-thumbnail'] : '',
        ];
    }
}

// Handle "Mark Complete" form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_complete_module'])) {
    $mark_idx = isset($_POST['module_idx']) ? intval($_POST['module_idx']) : -1;
    if ($mark_idx >= 0 && isset($modules[$mark_idx])) {
        // Store module completion in user meta
        update_user_meta($current_user->ID, "module_{$modules[$mark_idx]['id']}_completed", true);
        
        // Redirect to avoid form resubmission and update current_module param
        $redirect_url = add_query_arg('current_module', $mark_idx, get_permalink());
        wp_safe_redirect($redirect_url);
        exit;
    }
}

// Get user progress and unlocked modules count
$progress = intval(get_user_meta($current_user->ID, "gdlrms_course_progress_$course_id", true));
$unlocked_count = floor(count($modules) * ($progress / 100));
$unlocked_count = max(1, $unlocked_count);

// Current module index from query param or default
$current_module_idx = isset($_GET['current_module']) ? intval($_GET['current_module']) : 0;
if ($current_module_idx < 0 || $current_module_idx >= count($modules)) {
    $current_module_idx = 0;
}
if ($current_module_idx + 1 > $unlocked_count) {
    $current_module_idx = $unlocked_count - 1;
}

$current_module = (!empty($modules) && isset($modules[$current_module_idx])) ? $modules[$current_module_idx] : null;
?>
<style>
  /* Reset box sizing */
  *, *::before, *::after {
    box-sizing: border-box;
  }

  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(to bottom, rgb(20, 72, 161) 60%, white 40%);
    color:white;
    margin: 0;
    padding: 2rem 1rem;
  }

  .classroom-container {
    max-width: 1200px;
    margin: 0 auto;
  }

  h2 {
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 1rem;
    color:rgb(255, 255, 255);
    text-align: center;
  }

  
  /* Progress Bar */
  .progress-bar {
    background: #d0e1ff;
    height: 12px;
    border-radius: 8px;
    overflow: hidden;
    max-width: 600px;
    margin: 0 auto 2rem auto;
    box-shadow: inset 0 1px 3px rgba(0,74,173,0.3);
  }
  .progress-bar-fill {
    height: 100%;
    background: #004aad;
    width: 0%;
    border-radius: 8px;
    transition: width 0.5s ease;
  }

  /* Flex layout */
  .classroom-layout {
    display: flex;
    gap: 2rem;
    align-items: flex-start;
    justify-content: center;
    flex-wrap: nowrap;
  }

  /* Modules sidebar */
  .module-list {
    list-style: none;
    padding: 0;
    width: 320px;
    max-height: 75vh;
    overflow-y: auto;
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0,74,173,0.1);
    border: 1px solid #d9e3f5;
  }

  .module-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1rem;
    border-bottom: 1px solid #d9e3f5;
    cursor: pointer;
    transition: background-color 0.3s ease, box-shadow 0.3s ease;
    position: relative;
  }
  .module-item:last-child {
    border-bottom: none;
  }
  .module-item:hover:not(.locked) {
    background-color: #e6f0ff;
    box-shadow: 0 2px 8px rgba(0,74,173,0.15);
  }
  .module-item.locked {
    filter: grayscale(0.7);
    cursor: default;
    color: #777;
  }
  .module-item.selected {
    background-color: #cde1ff;
    font-weight: 700;
    box-shadow: 0 0 8px #004aadaa;
  }
  .module-item:focus-visible {
    outline: 3px solid #004aad;
    outline-offset: 2px;
  }

  .module-thumb {
    width: 72px;
    height: 44px;
    object-fit: cover;
    border-radius: 6px;
    background: #ddd;
    flex-shrink: 0;
  }
  .module-info {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
  }
  .module-title {
    font-size: 1.05rem;
    color: #004aad;
  }
  .module-status {
    font-size: 0.85rem;
    color: #666;
  }

  .lock-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 18px;
    color: #a0a0a0;
  }

  /* Video container */
  .current-video {
    flex-grow: 1;
    max-width: 720px;
    background: #002a66;
    border-radius: 12px;
    padding: 1.5rem 2rem;
    color: #fff;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
    box-shadow: 0 4px 24px rgba(0, 42, 102, 0.7);
  }

  .current-video h3 {
    margin: 0;
    font-size: 1.6rem;
    font-weight: 700;
    color: #ffd700;
    text-align: center;
  }

  .current-video video {
    width: 100%;
    max-height: 420px;
    border-radius: 10px;
    background: black;
    box-shadow: 0 0 15px #003366cc;
  }

  .current-video p {
    text-align: center;
    font-size: 1rem;
    color: #bbb;
  }

  /* Mark complete button */
  .current-video form {
    display: flex;
    justify-content: center;
    margin-top: 1rem;
  }

  .current-video button {
    background: #ffd700;
    border: none;
    color: #002a66;
    padding: 0.7rem 1.6rem;
    font-weight: 700;
    font-size: 1.1rem;
    border-radius: 10px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    box-shadow: 0 4px 10px #e6c200cc;
  }
  .current-video button:hover:not(:disabled) {
    background: #e6c200;
  }
  .current-video button:disabled,
  .current-video button[aria-disabled="true"] {
    background: #888;
    cursor: default;
    box-shadow: none;
    color: #444;
  }
  .quiz-btn {
  display: inline-block;
  background: #28a745;
  color: #fff;
  padding: 0.6rem 1.2rem;
  font-weight: bold;
  font-size: 1rem;
  text-decoration: none;
  border-radius: 8px;
  box-shadow: 0 4px 10px rgba(0, 128, 0, 0.2);
  transition: background 0.3s ease;
}
.quiz-btn:hover {
  background: #218838;
}
  /* Responsive: stack modules below video on narrow screens */
  @media (max-width: 900px) {
    .classroom-layout {
      flex-wrap: wrap;
    }
    .module-list {
      width: 100%;
      max-height: 300px;
      margin-bottom: 1.5rem;
      border-radius: 10px;
    }
    .current-video {
      max-width: 100%;
      padding: 1rem 1rem;
      box-shadow: none;
      border-radius: 8px;
    }
  }

  @media (max-width: 480px) {
    body {
      padding: 1rem;
    }
    h2 {
      font-size: 1.5rem;
    }
    .current-video h3 {
      font-size: 1.3rem;
    }
    .current-video button {
      width: 100%;
      font-size: 1rem;
      padding: 0.6rem;
    }
  }
</style>

<div class="classroom-container">
  <h2>Classroom – <?php echo esc_html(get_the_title($course_id)); ?></h2>

  <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?php echo esc_attr($progress); ?>">
    <div class="progress-bar-fill" style="width: <?php echo esc_attr($progress); ?>%;"></div>
  </div>

  <div class="classroom-layout">

    <ul class="module-list" id="module-list" role="list" aria-label="Course Modules">
      <?php
      $course_thumbnail_url = get_the_post_thumbnail_url($course_id, 'medium') ?: 'https://via.placeholder.com/120x68?text=No+Image';
       foreach ($modules as $i => $mod):
        $idx = $i + 1;
        $status = (get_user_meta($current_user->ID, "module_{$mod['id']}_completed", true)) ? 'completed' : (($idx <= $unlocked_count) ? 'unlocked' : 'locked');
        $thumb = !empty($mod['thumbnail']) ? $mod['thumbnail'] : ($course_thumbnail_url ? $course_thumbnail_url : 'https://via.placeholder.com/120x68?text=No+Image');
        $selected = ($i === $current_module_idx) ? 'selected' : '';
      ?>
      <li class="module-item <?php echo esc_attr("$status $selected"); ?>"
          <?php if ($status === 'unlocked' || $status === 'completed'): ?>
            onclick="location.href='<?php echo esc_url(add_query_arg('current_module', $i)); ?>'"
            tabindex="0"
            role="button"
            aria-pressed="<?php echo $selected ? 'true' : 'false'; ?>"
            onkeypress="if(event.key === 'Enter' || event.key === ' ') { location.href='<?php echo esc_url(add_query_arg('current_module', $i)); ?>'; event.preventDefault(); }"
          <?php else: ?>
            aria-disabled="true"
          <?php endif; ?>
      >
        <img src="<?php echo esc_url($thumb); ?>" alt="<?php echo esc_attr($mod['title'] ?: 'Lecture thumbnail'); ?>" class="module-thumb" loading="lazy" />
        <div class="module-info">
          <div class="module-title"><?php echo esc_html($mod['title']); ?></div>
          <div class="module-status"><?php echo ucfirst($status); ?></div>
        </div>
        <?php if ($status === 'locked'): ?>
          <span class="lock-icon" aria-hidden="true"><i class="fa fa-lock" aria-hidden="true"></i></span>
        <?php endif; ?>
      </li>
      <?php endforeach; ?>
    </ul>

    <section id="current" class="current-video" role="region" aria-live="polite" aria-label="Current module video player">
      <?php if ($current_module): ?>
      <?php
$url = $current_module['url'];
$ext = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);
$mime_types = [
    'mp4' => 'video/mp4',
    'webm' => 'video/webm',
    'mov' => 'video/quicktime',
    'avi' => 'video/x-msvideo',
    'ogg' => 'video/ogg',
    'm4v' => 'video/x-m4v',
];
$mime = $mime_types[strtolower($ext)] ?? 'video/mp4';
?>

<?php if (preg_match('/\.(mp4|webm|mov|avi|ogg|m4v)$/i', $url)): ?>
  <video id="current-video-player" controls preload="metadata" tabindex="0" aria-describedby="current-title">
    <source src="<?php echo esc_url($url); ?>" type="<?php echo esc_attr($mime); ?>" />
    Your browser does not support the video tag.
  </video>
<?php
$quiz_page = get_page_by_path('take-course-quiz');
$quiz_page_url = $quiz_page ? get_permalink($quiz_page->ID) : '';
?>

<?php if ($quiz_page_url): ?>
  <div style="text-align:center; margin-top: 1rem;">
    <a href="<?php echo esc_url( add_query_arg([
    'quiz_slug' => sanitize_title($current_module['title']),
    'cid' => $course_id
  ], $quiz_page_url ) ); ?>"
    class="quiz-btn" role="button">Take Quiz</a>
  </div>
<?php else: ?>
  <p style="color:red;">Quiz page not found. Please check the slug.</p>
<?php endif; ?>

<?php elseif (preg_match('/\.pdf$/i', $url)): ?>
  <iframe
    src="<?php echo esc_url($url); ?>#toolbar=1&navpanes=0&scrollbar=1"
    title="PDF Viewer"
    width="100%"
    height="500px"
    style="border: none; border-radius: 10px; box-shadow: 0 0 15px #003366cc;"
    aria-describedby="current-title"
  ></iframe>
<?php
$quiz_page = get_page_by_path('take-course-quiz');
$quiz_page_url = $quiz_page ? get_permalink($quiz_page->ID) : '';
?>

<?php if ($quiz_page_url): ?>
  <div style="text-align:center; margin-top: 1rem;">
  <a href="<?php echo esc_url( add_query_arg([
  'quiz_slug' => sanitize_title($current_module['title']),
  'cid' => $course_id
], $quiz_page_url ) ); ?>"

    class="quiz-btn" role="button">Take Quiz</a>
  </div>
<?php else: ?>
  <p style="color:red;">Quiz page not found. Please check the slug.</p>
<?php endif; ?>
<?php else: ?>
  <p>No video or PDF available for this module.</p>
<?php endif; ?>
<?php endif; ?>
    </section>

  </div>
</div>


