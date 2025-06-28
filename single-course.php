<?php get_header(); ?>
<?php
if (get_query_var('custom_course')) {
    $course_slug = get_query_var('custom_course');
    $course_post = get_page_by_path($course_slug, OBJECT, 'course');

    if ($course_post) {
        global $post;
        $post = $course_post;
        setup_postdata($post);
    }
}
?>

<?php
$course_id = get_the_ID();

// Enroll logic only if course is free, user is logged in, and not already enrolled
if (is_user_logged_in()) {
    $current_user_id = get_current_user_id();
    $enrolled_courses = get_user_meta($current_user_id, 'gdlrms_enrolled_courses', true);
    if (!is_array($enrolled_courses)) $enrolled_courses = [];

    $course_price = get_post_meta($course_id, 'gdlr-lms-course-price', true);
    $is_free = empty($course_price) || floatval($course_price) == 0;

    $already_enrolled = in_array($course_id, $enrolled_courses);

    if ($is_free && !$already_enrolled && isset($_GET['enroll']) && $_GET['enroll'] === '1') {
        $enrolled_courses[] = $course_id;
        update_user_meta($current_user_id, 'gdlrms_enrolled_courses', $enrolled_courses);

        // Optional: set default progress to 0
        update_user_meta($current_user_id, 'gdlrms_course_progress_' . $course_id, 0);

        // Optional: redirect to avoid resubmitting
        wp_redirect( get_permalink($course_id) . '?enrolled=1' );
        exit;
    }
}
?>

<?php
if (have_posts()) :
    while (have_posts()) :
        the_post();
        $course_type = $_GET['course_type'] ?? '';
        $course_page = (int) ($_GET['course_page'] ?? 0);

        $intro_video = '';
        $course_content = gdlr_lms_get_course_content_settings(get_the_ID());
        if (!empty($course_content)) {
            $first_section = $course_content[0];
            $lectures = json_decode($first_section['lecture-section'], true);
            if (!empty($lectures)) {
                $first_lecture = $lectures[0];
                $intro_raw = $first_lecture['lecture-content'] ?? '';
                if (preg_match('/https?:\/\/[^"]+\.(mp4|mov|webm|ogg|m4v|avi)/i', $intro_raw, $matches)) {
                    $intro_video = esc_url($matches[0]);
                }
            }
        }

        $category = get_the_category();
        $category_id = !empty($category) ? $category[0]->term_id : 0;
        $related_args = array(
            'post_type' => 'course',
            'posts_per_page' => 3,
            'post__not_in' => array(get_the_ID()),
            'cat' => $category_id
        );
        $related_courses = new WP_Query($related_args);
?>
<?php
$current_user = wp_get_current_user();
$is_paid = (float) get_post_meta(get_the_ID(), 'gdlr-lms-course-price', true) > 0;

if (!is_user_logged_in()) {
    $link = site_url('/signup?redirect=' . ($is_paid ? 'paywall' : 'course') . '&course_id=' . get_the_ID());
} else {
    // Check if user has bought course
    //$purchased = gdlr_lms_has_course_access(get_the_ID()); 
    /*if ($purchased) {
        $link = get_permalink();
    } else {
        $link = $is_paid
            ? site_url('/paywall?course_id=' . get_the_ID())
            : get_permalink();
    } **/
}
?>
<style>
.course-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.course-sidebar {
    flex: 1;
    max-width: 300px;
    background: #f9f9f9;
    padding: 1.5rem;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
.course-title{
    color:white;
}
.course-sidebar h3 {
    margin-bottom: 1rem;
}

.course-sidebar ul {
    list-style: none;
    padding: 0;
}

.course-sidebar ul li {
    margin-bottom: 0.8rem;
}

.enroll-btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.75rem 1.25rem;
    background-color: #0073aa;
    color: white;
    text-decoration: none;
    border-radius: 5px;
}

.enroll-btn:hover {
    background-color: #005e8a;
}

.course-details {
    flex: 3;
    min-width: 0;
}

.course-title {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.video-wrapper {
    margin-bottom: 1.5rem;
}

.video-wrapper video,
.video-wrapper img {
    width: 100%;
    border-radius: 8px;
    max-height: 450px;
    object-fit: cover;
}

.course-meta {
    font-size: 0.9rem;
    color: #555;
    margin-bottom: 1rem;
}

.share-buttons {
    display: flex;
    gap: 1rem;
    margin: 1.5rem 0;
    align-items: center;
    flex-wrap: wrap;
}

.share-buttons a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: #0073aa;
    background: #f0f0f0;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: background 0.3s;
}

.share-buttons a:hover {
    background: #e0e0e0;
}

.share-buttons svg {
    width: 18px;
    height: 18px;
    fill: #0073aa;
}

.course-content {
    line-height: 1.7;
    font-size: 1rem;
    color: #333;
}
#course-reviews {
    background: #f7f7f7;
    padding: 2rem;
    border-radius: 10px;
    margin-top: 3rem;
}

#course-reviews h2 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.comment-form textarea,
.comment-form input {
    width: 100%;
    padding: 0.75rem;
    margin-bottom: 1rem;
    border-radius: 5px;
    border: 1px solid #ccc;
}

.comment-form input[type="submit"] {
    background-color: #0073aa;
    color: white;
    border: none;
    cursor: pointer;
    padding: 0.75rem 1.5rem;
    border-radius: 5px;
}

.comment-form input[type="submit"]:hover {
    background-color: #005e8a;
}


/* 🔻 Mobile responsiveness below 768px */
@media (max-width: 768px) {
    .course-preview {
        flex-direction: column;
    }

    .course-sidebar {
        max-width: 100%;
        width: 100%;
        order: 2;
    }

    .course-details {
        width: 100%;
        order: 1;
        background-color: #000;
        padding: 1rem;
        border-radius: 10px;
    }

    .course-title,
    .course-meta,
    .course-details,
    .course-modules h2,
    .course-modules li,
    .course-sidebar h3 {
        color: #fff !important;
    }

    .share-buttons {
        justify-content: flex-start;
        flex-direction: column;
        align-items: flex-start;
    }

    .share-buttons a {
        width: 100%;
        background: transparent !important;
        color: #fff !important;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .share-buttons svg {
        fill: #fff !important;
    }
}


</style>

<div class="course-preview">
    <aside class="course-sidebar">
        <h3>Course Details</h3>
        <ul>
            <li><strong>Level:</strong> <?= get_post_meta(get_the_ID(), 'course_level', true); ?></li>
            <li><strong>Duration:</strong> <?= get_post_meta(get_the_ID(), 'course_duration', true); ?></li>
            <li><strong>Lessons:</strong>
                <?php
                $lessons = get_post_meta(get_the_ID(), 'course_lessons', true);
                $lesson_array = json_decode($lessons, true);
                echo is_array($lesson_array) ? count($lesson_array) : 0;
                ?>
            </li>
            <li><strong>Price:</strong>
                <?php
                $course_price = get_post_meta(get_the_ID(), 'gdlr-lms-course-price', true);
                echo empty($course_price) || $course_price == 0 ? 'Free' : '$' . number_format($course_price, 2);
                ?>
            </li>
        </ul>
        <?php if (!is_user_logged_in()) : ?>
    <a href="<?= esc_url(wp_login_url(get_permalink())) ?>" class="enroll-btn">Login to Enroll</a>

<?php elseif ($is_free && !$already_enrolled) : ?>
    <a href="<?= esc_url(add_query_arg('enroll', '1')) ?>" class="enroll-btn">Enroll for Free</a>

<?php elseif ($already_enrolled) : ?>
    <a href="<?= esc_url(get_permalink()) ?>" class="enroll-btn">Go to Course</a>

<?php elseif ($is_paid) : ?>
    <a href="<?= esc_url(site_url('/paywall?course_id=' . $course_id)) ?>" class="enroll-btn">Buy Course</a>
<?php endif; ?>

    </aside>

    <main class="course-details">
        <h1 class="course-title"><?php the_title(); ?></h1>

        <div class="video-wrapper">
            <?php if (!empty($intro_video)) : ?>
                <video controls playsinline>
                    <source src="<?= $intro_video; ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            <?php elseif (has_post_thumbnail()) : ?>
                <?= get_the_post_thumbnail(null, 'large', ['class' => 'course-thumbnail']); ?>
            <?php else : ?>
                <p>No intro video or thumbnail.</p>
            <?php endif; ?>
        </div>

        <!-- Course Modules -->
        <?php if (!empty($course_content)) : ?>
            <div class="course-modules">
                <h2>Course Modules</h2>
                <ul>
                    <?php
                    foreach ($course_content as $section) {
                        echo '<li><strong>' . esc_html($section['section-name']) . '</strong><ul>';
                        $lectures = json_decode($section['lecture-section'], true);
                        if (!empty($lectures)) {
                         foreach ($lectures as $lecture) {
                        $lecture_title = $lecture['lecture-title'] ?? 'Untitled Lecture';
                        echo '<li>' . esc_html($lecture_title) . '</li>';
                    }
                        }
                        echo '</ul></li>';
                    }
                    ?>
                </ul>
            </div>
        <?php else : ?>
            <p>No course modules available.</p>
        <?php endif; ?>

        <div class="course-meta">
            <strong>Instructor:</strong> <?= get_the_author(); ?> |
            <strong>Course Created:</strong> <?= get_the_date(); ?>
        </div>

                <div class="share-buttons">
            <span>Share:</span>

            <a href="https://facebook.com/sharer/sharer.php?u=<?= urlencode(get_permalink()); ?>" target="_blank">
                <!-- Facebook Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M22 12a10 10 0 1 0-11.5 9.9v-7h-2v-3h2v-2.3c0-2 1.2-3.1 3-3.1.9 0 1.8.2 1.8.2v2h-1c-1 0-1.3.6-1.3 1.2V11h2.3l-.4 3H14v7A10 10 0 0 0 22 12z"/>
                </svg>
                Facebook
            </a>

            <a href="https://twitter.com/intent/tweet?url=<?= urlencode(get_permalink()); ?>&text=<?= urlencode(get_the_title()); ?>" target="_blank">
                <!-- Twitter Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M22.46 6c-.77.35-1.6.59-2.46.7a4.26 4.26 0 0 0 1.88-2.35c-.82.5-1.73.85-2.7 1.04a4.22 4.22 0 0 0-7.2 3.84 12 12 0 0 1-8.7-4.4 4.23 4.23 0 0 0 1.3 5.64A4.21 4.21 0 0 1 2.8 9.7v.05a4.22 4.22 0 0 0 3.38 4.13 4.27 4.27 0 0 1-1.91.07 4.23 4.23 0 0 0 3.95 2.93A8.49 8.49 0 0 1 2 19.54a12 12 0 0 0 6.29 1.84c7.55 0 11.68-6.26 11.68-11.68l-.01-.53A8.4 8.4 0 0 0 22.46 6z"/>
                </svg>
                Twitter
            </a>

            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(get_permalink()); ?>&title=<?= urlencode(get_the_title()); ?>" target="_blank">
                <!-- LinkedIn Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                    <path d="M4.98 3.5C4.98 4.88 3.87 6 2.5 6S0 4.88 0 3.5 1.12 1 2.5 1 4.98 2.12 4.98 3.5zM.5 8h4V24h-4V8zM8 8h3.6v2.2h.1c.5-.9 1.6-1.8 3.4-1.8 3.6 0 4.3 2.4 4.3 5.5V24h-4v-8.2c0-2-.1-4.5-2.8-4.5-2.8 0-3.2 2.2-3.2 4.3V24h-4V8z"/>
                </svg>
                LinkedIn
            </a>
        </div>
          <div id="course-reviews" style="margin-top: 3rem;">
    <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Student Reviews & Comments</h2>
      
    <?php


if (comments_open() || get_comments_number()) :
?>
<div id="comments" class="comments-area">

    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            printf(_nx('One comment', '%1$s comments', get_comments_number(), 'comments title', 'your-textdomain'),
                number_format_i18n(get_comments_number()));
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style'      => 'ol',
                'short_ping' => true,
            ));
            ?>
        </ol>

        <?php the_comments_navigation(); ?>

    <?php endif; ?>

    <?php
    if (!comments_open()) :
        echo '<p class="no-comments">Comments are closed.</p>';
    endif;

    comment_form();
    ?>

</div>

<?php
else :
    echo '<div style="border: 1px solid blue;">Comments are closed.</div>';
 
endif;

    ?>
</div>
    </main>
  
</div>


<?php endwhile; endif; ?>

<?php get_footer(); ?>
