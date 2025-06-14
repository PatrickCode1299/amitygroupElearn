<?php
get_header();

$category = get_queried_object();

$paged = get_query_var('paged') ? get_query_var('paged') : 1;

$args = [
    'post_type' => 'course',
    'posts_per_page' => 6,
    'paged' => $paged,
    'tax_query' => [
        [
            'taxonomy' => 'course_category',
            'field'    => 'term_id',
            'terms'    => $category->term_id,
        ],
    ],
];

$query = new WP_Query($args);
?>

<style>
.course-category-hero {
    position: relative;
    background-image: url('https://images.unsplash.com/photo-1603574670812-d24560880210?auto=format&fit=crop&w=1600&q=80'); /* Beautiful blue-themed learning image */
    background-size: cover;
    background-position: center;
    height: 400px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 3rem;
    border-radius: 0 0 40px 40px;
    overflow: hidden;
    box-shadow: 0 10px 20px rgba(0, 74, 173, 0.2);
}

.course-category-hero .overlay {
    background: rgba(0, 0, 0, 0.5);
    width: 100%;
    height: 100%;
    padding: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.course-category-hero .content {
    text-align: center;
    color: #ffffff;
    max-width: 700px;
}

.course-category-hero .content h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #ffffff;
    font-weight: 700;
}

.course-category-hero .content p {
    font-size: 1.2rem;
    line-height: 1.6;
    color: #f1f1f1;
}

.course-category-hero::before {
    content: "";
    position: absolute;
    top: 0; left: 0;
    height: 100%; width: 100%;
    background: rgba(0, 0, 0, 0.5);
}
.course-category-hero .content {
    position: relative;
    max-width: 800px;
    margin: 0 auto;
}
.course-category-hero h1 {
    font-size: 3rem;
    margin-bottom: 1rem;
    font-weight: 700;
}
.course-category-hero p {
    font-size: 1.2rem;
    line-height: 1.8;
}

/* Course Grid */
.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 2rem;
    padding: 4rem 2rem;
    max-width: 1200px;
    margin: 0 auto;
}
.course-card {
    background-color: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    transition: transform 0.2s ease;
}
.course-card:hover {
    transform: translateY(-5px);
}
.course-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.course-card .info {
    padding: 1rem;
}
.course-card .info h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
    color: #00377c;
}
.course-card .info a {
    display: inline-block;
    margin-top: 0.5rem;
    color: #004aad;
    text-decoration: underline;
    font-weight: 500;
}

/* Pagination */
.pagination {
    text-align: center;
    margin-bottom: 3rem;
}
.pagination .page-numbers {
    display: inline-block;
    margin: 0 5px;
    padding: 8px 14px;
    border-radius: 6px;
    background-color: #f4f8ff;
    color: #004aad;
    text-decoration: none;
    transition: background-color 0.2s;
}
.pagination .current {
    background-color: #004aad;
    color: white;
    font-weight: bold;
}
.pagination .page-numbers:hover {
    background-color: #cddfff;
}
</style>

<section class="course-category-hero">
    <div class="content">
        <h1><?php echo esc_html($category->name); ?> Courses</h1>
        <p>Learn more about <strong><?php echo esc_html($category->name); ?></strong> from well-curated lessons and professional experts in this field. Start your journey toward growth and mastery today.</p>
    </div>
</section>

<section class="course-grid">
    <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
        <div class="course-card">
            <a href="<?php the_permalink(); ?>">
                <img src="<?= esc_url(get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: 'https://placehold.co/600x400?text=No+Image') ?>" alt="<?php the_title_attribute(); ?>">
            </a>
            <div class="info">
                <h3><?php the_title(); ?></h3>
                <a href="<?php the_permalink(); ?>">View Course</a>
            </div>
        </div>
    <?php endwhile; else: ?>
        <p style="text-align:center;">No courses available in this category yet.</p>
    <?php endif; ?>
</section>

<?php if ($query->max_num_pages > 1): ?>
    <div class="pagination">
        <?php
// Pagination
$total_pages = $query->max_num_pages;
if ($total_pages > 1) {
    echo '<div class="pagination">';
    echo paginate_links([
        'total' => $total_pages,
        'current' => $paged,
        'prev_text' => '« Prev',
        'next_text' => 'Next »',
    ]);
    echo '</div>';
}
wp_reset_postdata();
?>

    </div>
<?php endif; ?>

<?php
wp_reset_postdata();
get_footer();
