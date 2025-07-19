<?php
/**
 * Template Name: All Courses
 */

get_header();

$current_category = get_queried_object();
$term_id = $current_category->term_id ?? null;

// Group courses by category if available
$courses_by_category = [];
if ($term_id) {
    $args = [
        'post_type' => 'course',
        'posts_per_page' => -1,
        'tax_query' => [
            [
                'taxonomy' => 'course_category',
                'field' => 'term_id',
                'terms' => $term_id,
            ]
        ]
    ];
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $cats = get_the_terms(get_the_ID(), 'course_category');
            if ($cats && !is_wp_error($cats)) {
                foreach ($cats as $cat) {
                    $courses_by_category[$cat->name][] = get_post();
                }
            }
        }
    }
    wp_reset_postdata();
}

// Recommendation fallback logic
$recommended_courses = [];
$recommended_cat = null;
if (empty($courses_by_category)) {
    $alt_cats = get_terms([
        'taxonomy' => 'course_category',
        'exclude' => [$term_id],
        'hide_empty' => true,
    ]);
    foreach ($alt_cats as $alt_cat) {
        $alt_args = [
            'post_type' => 'course',
            'posts_per_page' => 6,
            'tax_query' => [
                [
                    'taxonomy' => 'course_category',
                    'field' => 'term_id',
                    'terms' => $alt_cat->term_id,
                ]
            ]
        ];
        $alt_query = new WP_Query($alt_args);
        if ($alt_query->have_posts()) {
            $recommended_cat = $alt_cat;
            $recommended_courses = $alt_query->posts;
            wp_reset_postdata();
            break;
        }
    }
    $cat_link = $recommended_cat ? get_term_link($recommended_cat) : '';
}
                
?>

<style>
.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 2rem;
}
.course-card {
    background: #fff;
    border: 1px solid #d8e6ff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s;
}
.course-card:hover {
    transform: translateY(-5px);
}
.course-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
    border-bottom: 1px solid #ddd;
}
.course-card-content {
    padding: 1rem;
}
.course-card-content h3 {
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    color: #114a8e;
}
.course-card-content .meta {
    font-size: 0.85rem;
    color: #777;
    margin-bottom: 0.4rem;
}
.course-card-content .price {
    font-weight: bold;
    color: #1a73e8;
}
.preview-link {
    display: block;
    background: #1a73e8;
    color: #fff;
    text-align: center;
    padding: 0.7rem;
    text-decoration: none;
    border-top: 1px solid #d8e6ff;
    font-weight: 500;
    border-bottom-left-radius: 12px;
    border-bottom-right-radius: 12px;
}
.preview-link:hover {
    background: #0f5ab5;
}

</style>
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
        <h1><?php echo esc_html($current_category->name); ?> Courses</h1>
        <p>Learn more about <strong><?php echo esc_html($current_category->name); ?></strong> from well-curated lessons and professional experts in this field. Start your journey toward growth and mastery today.</p>
    </div>
</section>


<div class="gdlr-core-pbf-wrapper">
    <div class="gdlr-core-pbf-wrapper-content gdlr-core-js">
        <div class="gdlr-core-pbf-wrapper-container clearfix gdlr-core-container">
            <div class="gdlr-core-pbf-element">
                <div class="gdlr-core-title-item gdlr-core-item-pdb clearfix gdlr-core-center-align gdlr-core-title-item-caption-top gdlr-core-item-pdlr">
                    <div class="gdlr-core-title-item-title-wrap">
                      
                    </div>
                </div>
            </div>

            <?php if (!empty($courses_by_category)): ?>
                <?php foreach ($courses_by_category as $category => $courses): ?>
                    <div class="gdlr-core-pbf-element">
                        <h3 style="font-size: 28px; margin-bottom:0px; text-align:center;"><?php echo esc_html($category); ?></h3>
                        <section class="course-grid">
                            <?php foreach ($courses as $course):
                                $thumb = get_the_post_thumbnail_url($course->ID, 'medium') ?: 'https://cdn-icons-png.flaticon.com/512/3135/3135768.png';
                                $settings_raw = get_post_meta($course->ID, 'gdlr-lms-course-settings', true);
                                $settings = json_decode($settings_raw, true);
                                $actual_price = isset($settings['price']) ? floatval($settings['price']) : 0;
                                $main_price = isset($settings['discount-price']) ? floatval($settings['discount-price']) : 0;
                                $display_price = $main_price > 0 ? $main_price : $actual_price;

                            ?>
                            <div class="course-card">
                                <img src="<?php echo esc_url($thumb); ?>" alt="Course thumbnail">
                                <div class="course-card-content">
                                    <h3><?php echo esc_html($course->post_title); ?></h3>
                                    <div class="meta"><?php echo strip_tags(get_the_term_list($course->ID, 'course_category', '', ', ')); ?></div>
                                   <div class="price"><?php echo $display_price > 0 ? '$' . number_format($display_price, 2) : 'Free'; ?></div>

                                </div>
                                <a class="preview-link" href="<?php echo get_permalink($course->ID); ?>">Preview Course</a>
                            </div>
                            <?php endforeach; ?>
                        </section>
                    </div>
                <?php endforeach; ?>
            <?php elseif (!empty($recommended_courses)): ?>
                <div style="text-align:center; margin-top: 2rem; background-color:#eaf3ff; padding:2rem; border-radius:12px; display:block;">
                    <h3 style="margin-bottom:0.5rem; display:block;">You might like <a href="<?php echo esc_url($cat_link); ?>"><?php echo esc_html($recommended_cat->name); ?> Courses</a></h3>
                    <p style="margin-top:0; display:block;">We don’t currently have courses in <?php echo esc_html($current_category->name); ?>, but check out our <a href="<?php echo esc_url($cat_link); ?>"><?php echo esc_html($recommended_cat->name); ?></a> category — it’s packed with valuable content!</p>
                    <div class="course-grid">
                        <?php foreach ($recommended_courses as $course):
                            $thumb = get_the_post_thumbnail_url($course->ID, 'medium') ?: 'https://cdn-icons-png.flaticon.com/512/3135/3135768.png';
                            $settings_raw = get_post_meta($course->ID, 'gdlr-lms-course-settings', true);
                            $settings = json_decode($settings_raw, true);
                            $actual_price = isset($settings['price']) ? floatval($settings['price']) : 0;
                            $main_price = isset($settings['discount-price']) ? floatval($settings['discount-price']) : 0;
                            $display_price = $main_price > 0 ? $main_price : $actual_price;

                        ?>
                        <div class="course-card" style="border: 1px solid #b4d7ff;">
                            <img src="<?php echo esc_url($thumb); ?>" alt="Course thumbnail">
                            <div class="course-card-content">
                                <h3><?php echo esc_html($course->post_title); ?></h3>
                                <div class="meta"><?php echo strip_tags(get_the_term_list($course->ID, 'course_category', '', ', ')); ?></div>
                                <div class="price"><?php echo $display_price > 0 ? '$' . number_format($display_price, 2) : 'Free'; ?></div>

                            </div>
                            <a class="preview-link" href="<?php echo get_permalink($course->ID); ?>">Preview Course</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <p style="text-align:center;">No courses available in this category yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?>
