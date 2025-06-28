<?php
/*
if (is_user_logged_in()) {
    wp_redirect(site_url('/user-dashboard'));
    exit;
} **/
?>
<?php get_header(); ?>

<style>
section {
    padding: 4rem 2rem;
}

.skills-section,
.learners-section,
.objectives,
.testimonials {
    padding: 3rem 1.5rem;
}
.hero {
    background: linear-gradient(to right, #004aad, #007bff);
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    padding: 4rem 2rem;
    color: white;
}

.hero .text {
    flex: 1;
    min-width: 300px;
    padding-right: 2rem;
}

.hero .text h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 700;
}

.hero .text p {
    font-size: 1.2rem;
    line-height: 1.8;
    max-width: 500px;
}

.hero .image {
    flex: 1;
    min-width: 300px;
    text-align: center;
}

.hero .image img {
    max-width: 100%;
    height: auto;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

/* Objectives Section */
.objectives {
    padding: 5rem 2rem;
    background-color: #ffffff;
    text-align: center;
}

.objectives h2 {
    color: #004aad;
    font-size: 2.2rem;
    margin-bottom: 2.5rem;
}

.objectives .grid {
    display: flex;
    flex-wrap: wrap;
    gap: 2rem;
    justify-content: center;
}

.objectives .item {
    background-color: #f4f8ff;
    padding: 2rem;
    border-radius: 12px;
    width: 300px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
    transition: transform 0.3s ease;
}

.objectives .item:hover {
    transform: translateY(-8px);
}

.objectives .item h3 {
    color: #004aad;
    font-size: 1.25rem;
    margin-bottom: 0.5rem;
}

/* Testimonials */
.testimonials {
    background: linear-gradient(to right, #004aad, #007bff);
    color: white;
    padding: 5rem 2rem;
    text-align: center;
}

.testimonials h2 {
    margin-bottom: 2rem;
    font-size: 2rem;
}

.testimonials .testimonial {
    max-width: 600px;
    margin: 0 auto 2rem;
    font-style: italic;
    font-size: 1.1rem;
    line-height: 1.8;
}

.testimonials .testimonial cite {
    display: block;
    margin-top: 1rem;
    font-style: normal;
    font-weight: bold;
    color: #e0e0e0;
}
/* Popular Skills */
.skills-section {
    background-color: #f9fbff;
    padding: 4rem 2rem;
    text-align: center;
}

.skills-section h2 {
    color: #004aad;
    font-size: 2rem;
    margin-bottom: 2rem;
}
.skills-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 2rem;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

.skills-grid a {
    background-color: #ffffff;
    padding: 2rem 1rem;
    border-radius: 12px;
    text-decoration: none;
    color: #004aad;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    transition: transform 0.25s ease, box-shadow 0.3s ease;
    font-weight: 600;
    font-size: 1rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    min-height: 180px;
    justify-content: center;
}

.skills-grid a:hover {
    transform: translateY(-5px) scale(1.02);
    background-color: #f0f8ff;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.skills-grid a i {
    font-size: 2rem;
    margin-bottom: 0.75rem;
    color: #007bff;
}


/* Learners Viewing */
.learners-section {
    padding: 4rem 2rem;
    background-color: #f9fbff;
    text-align: center;
}

.learners-section h2 {
    color: #004aad;
    font-size: 2.2rem;
    margin-bottom: 2.5rem;
}

.learners-carousel {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    scroll-snap-type: x mandatory;
    padding-left: 1rem;
}

.course-card {
    flex: 0 0 auto;
    width: 280px;
    background-color: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    scroll-snap-align: start;
}

.course-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
}

.course-card a {
    text-decoration: none;
    color: inherit;
    display: block;
}

.course-card .card-image {
    width: 100%;
    height: 180px;
    background-size: cover;
    background-position: center;
}

.course-card .card-content {
    padding: 1rem 1rem 1.5rem;
    text-align: left;
    position: relative;
}

.course-card h4 {
    font-size: 1.1rem;
    font-weight: 600;
    color: #002c6a;
    margin-bottom: 0.5rem;
}

.course-card p {
    font-size: 0.9rem;
    color: #555;
}

.course-card .badge {
    background: #e0f0ff;
    color: #007bff;
    font-size: 0.7rem;
    font-weight: bold;
    text-transform: uppercase;
    padding: 0.3rem 0.6rem;
    border-radius: 6px;
    position: absolute;
    top: -12px;
    left: 1rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.skill-card {
    position: relative;
    padding-bottom: 3rem; /* to make room for the button */
}

.skill-card .start-now-btn {
    margin-top: 1rem;
    padding: 0.4rem 1rem;
    background-color: #007bff;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.skill-card .start-now-btn:hover {
    background-color: #0056b3;
}

@media (max-width: 768px) {
  .hero {
    flex-direction: column;
    text-align: center;
    padding: 3rem 1rem;
  }

  .hero .text {
    padding-right: 0;
    padding-bottom: 2rem;
  }

  .hero .text h1 {
    font-size: 2rem;
  }

  .hero .text p {
    font-size: 1rem;
    max-width: 100%;
  }

  .hero .image {
    padding: 0 1rem;
  }
}
.hero .text {
    flex: 1;
    min-width: 300px;
    padding-right: 2rem;
    max-width: 600px; /* Optional: helps prevent overflow */
}
</style>

<!-- HERO SECTION -->
<section class="hero" data-aos="fade-up">
    <div class="text">
        <h1>Start Here, Learn a Skill With AmityGroupTrainers</h1>
        <p>Empowering professionals, organizations, and individuals through world-class training. Grow, transform, and lead with Amity.</p>
    </div>
    <div class="image">
        <img src="https://images.stockcake.com/public/7/e/a/7ea17dad-b620-48de-99ea-6b49a2c3e861_large/corporate-training-session-stockcake.jpg" alt="Modern corporate training session">
    </div>
</section>
<section class="skills-section" data-aos="fade-right">
    <h2>All the Skills You Need in One Place</h2>
    <div class="skills-grid">
      <?php
$icons = ['fa-laptop-code', 'fa-lightbulb', 'fa-users', 'fa-chalkboard-teacher', 'fa-database', 'fa-briefcase', 'fa-robot', 'fa-network-wired', 'fa-flask', 'fa-brain'];
shuffle($icons);

$categories = get_terms([
    'taxonomy' => 'course_category',
    'hide_empty' => false,
]);

if (!empty($categories) && !is_wp_error($categories)) :
    $i = 0;
    foreach ($categories as $category) :
        $link = get_term_link($category);
        $icon = $icons[$i % count($icons)];
        $i++;
        ?>
        <a href="<?= esc_url($link); ?>" class="skill-card">
            <i class="fa-solid <?= esc_attr($icon); ?>"></i>
            <div><?= esc_html($category->name); ?></div>
            <button class="start-now-btn">Start Now</button>
        </a>
    <?php endforeach;
else : ?>
    <p>No course categories found.</p>
<?php endif; ?>

    </div>
</section>

<!-- OBJECTIVES SECTION -->
<section class="objectives" data-aos="fade-up">
    <h2>What We Offer</h2>
    <div class="grid">
        <div class="item">
            <h3>Corporate IT & Supply Chain</h3>
            <p>Equipping staff with the latest digital tools and operational strategies.</p>
        </div>
        <div class="item">
            <h3>Diversity Awareness</h3>
            <p>Inclusive training for doctors, lawyers, and legal professionals.</p>
        </div>
        <div class="item">
            <h3>Professional Development</h3>
            <p>Advance your career with upskilling opportunities in education and beyond.</p>
        </div>
        <div class="item">
            <h3>Healthcare Coaching</h3>
            <p>Personalized learning paths for modern medical professionals.</p>
        </div>
        <div class="item">
            <h3>Organizational Growth</h3>
            <p>Strategic workforce development for dynamic organizations.</p>
        </div>
        <div class="item">
            <h3>Workforce Upskilling</h3>
            <p>Build capacity across teams through structured, goal-driven learning.</p>
        </div>
    </div>
</section>
<section class="learners-section">
    <h2>Learners Are Viewing</h2>
    <div class="learners-carousel">
        <?php
        $recent_courses = new WP_Query([
            'post_type' => 'course',
            'posts_per_page' => 5,
            'post_status' => 'publish'
        ]);

        if ($recent_courses->have_posts()) :
            while ($recent_courses->have_posts()) : $recent_courses->the_post();
                $thumbnail_url = get_the_post_thumbnail_url(get_the_ID(), 'medium');
                ?>
                <div class="course-card">
                    <a href="<?php the_permalink(); ?>">
                        <div class="card-image" style="background-image: url('<?= esc_url($thumbnail_url ?: 'https://placehold.co/300x180?text=No+Image') ?>');"></div>
                        <div class="card-content">
                            <span class="badge">Trending</span>
                            <h4><?php the_title(); ?></h4>
                            <p>Explore this course to enhance your skills.</p>
                        </div>
                    </a>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            echo '<p>No courses found.</p>';
        endif;
        ?>
    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="testimonials" data-aos="zoom-in">
    <h2>What Our Learners Say</h2>
    <div class="testimonial">
        “AmityGroupTrainers helped us build a stronger, more tech-savvy team. Their courses are engaging and very relevant to our industry.”
        <cite>– Dr. Ahmed Bello, Hospital Administrator</cite>
    </div>
    <div class="testimonial">
        “Their diversity awareness program completely changed how we approach team collaboration in our firm.”
        <cite>– Grace O., Legal Consultant</cite>
    </div>
</section>

<?php get_footer(); ?>
