<?php get_header(); ?>

<style>
.course-preview {
    display: flex;
    flex-wrap: wrap;
    gap: 3rem;
    padding: 4rem 2rem;
    background-color: #f9f9f9;
}
.course-details {
    flex: 2;
}
.course-sidebar {
    flex: 1;
    background: white;
    padding: 2rem;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.course-thumbnail img {
    width: 100%;
    border-radius: 12px;
    margin-bottom: 1rem;
}
.course-title {
    font-size: 2rem;
    color: #004aad;
    margin-bottom: 1rem;
}
.course-meta {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 1.5rem;
}
.course-content {
    line-height: 1.8;
}
.course-sidebar h3 {
    font-size: 1.2rem;
    margin-bottom: 1rem;
}
.course-sidebar ul {
    list-style: none;
    padding: 0;
}
.course-sidebar ul li {
    margin-bottom: 0.75rem;
    font-size: 0.95rem;
}
.enroll-btn {
    display: inline-block;
    margin-top: 1rem;
    padding: 0.75rem 1.5rem;
    background-color: #004aad;
    color: white;
    text-align: center;
    text-decoration: none;
    border-radius: 8px;
    transition: background 0.3s ease;
}
.enroll-btn:hover {
    background-color: #002c6f;
}
</style>

<div class="course-preview">
    <div class="course-details">
        <div class="course-thumbnail">
            <?php if (has_post_thumbnail()) {
                the_post_thumbnail('large');
            } ?>
        </div>
        <h1 class="course-title"><?php the_title(); ?></h1>
        <div class="course-meta">
            <strong>Instructor:</strong> <?php echo get_the_author(); ?> <br>
            <strong>Published:</strong> <?php echo get_the_date(); ?>
        </div>
        <div class="course-content">
            <?php the_content(); ?>
        </div>
    </div>

    <div class="course-sidebar">
        <h3>Course Details</h3>
        <ul>
            <li><strong>Level:</strong> <?php echo get_post_meta(get_the_ID(), 'course_level', true); ?></li>
            <li><strong>Duration:</strong> <?php echo get_post_meta(get_the_ID(), 'course_duration', true); ?></li>
            <li><strong>Lessons:</strong> <?php echo count(get_post_meta(get_the_ID(), 'course_lessons', true)); ?></li>
        </ul>
        <a href="#" class="enroll-btn">Enroll Now</a>
    </div>
</div>

<?php get_footer(); ?>
