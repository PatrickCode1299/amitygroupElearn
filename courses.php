<?php
/* Template Name: All Courses */
get_header();

// Get all course categories
$categories = get_terms([
    'taxonomy' => 'course_category',
    'hide_empty' => false
]);

// Get course enrollment counts per category
$category_enrollment = [];
foreach ($categories as $cat) {
    $args = [
        'post_type' => 'course',
        'posts_per_page' => -1,
        'tax_query' => [[
            'taxonomy' => 'course_category',
            'field' => 'term_id',
            'terms' => $cat->term_id
        ]],
    ];
    $posts = get_posts($args);
    $enrollment = 0;
    foreach ($posts as $p) {
        $users = get_post_meta($p->ID, 'enrolled_users', true);
        $enrollment += is_array($users) ? count($users) : 0;
    }
    $category_enrollment[$cat->term_id] = [
        'count' => $enrollment,
        'name' => $cat->name,
        'id' => $cat->term_id
    ];
}

usort($category_enrollment, function ($a, $b) {
    return $b['count'] <=> $a['count'];
});
$top_categories = array_slice($category_enrollment, 0, 4);

// Handle search and filtering
$search_term = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
$selected_cat = isset($_GET['category']) ? intval($_GET['category']) : '';
$order_by_price = isset($_GET['order']) && $_GET['order'] === 'price';

$args = [
    'post_type' => 'course',
    's' => $search_term,
    'posts_per_page' => -1,
];

if ($selected_cat) {
    $args['tax_query'] = [[
        'taxonomy' => 'course_category',
        'field' => 'term_id',
        'terms' => $selected_cat
    ]];
}

$courses = get_posts($args);

if ($order_by_price) {
    usort($courses, function($a, $b) {
        $price_a = floatval(get_post_meta($a->ID, 'price', true));
        $price_b = floatval(get_post_meta($b->ID, 'price', true));
        return $price_a <=> $price_b;
    });
}
?>

<link href="https://site-assets.fontawesome.com/releases/v6.7.2/css/all.css" rel="stylesheet">
<style>
body {
    background: #e5ecfa;
    font-family: 'Segoe UI', sans-serif;
}
.hero-section {
    background: linear-gradient(rgba(0, 40, 100, 0.85), rgba(0, 40, 100, 0.85)),
                url('<?php echo get_template_directory_uri(); ?>/assets/images/learning-bg.jpg') center/cover no-repeat;
    color: white;
    text-align: center;
    padding: 6rem 2rem 3rem;
}
.hero-section h1 {
    font-size: 3.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}
.search-bar {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
    position: relative;
}
.search-bar input {
    width: 85%;
    max-width: 700px;
    padding: 1rem 1.5rem;
    border-radius: 50px;
    border: none;
    font-size: 1rem;
    box-shadow: 0 0 10px rgba(255,255,255,0.2);
    outline: none;
}
#search-suggestions {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    color: #000;
    z-index: 999;
    width: 85%;
    max-width: 700px;
    border-radius: 0 0 10px 10px;
    overflow: hidden;
}
.section-title {
    text-align: center;
    font-size: 2.2rem;
    font-weight: bold;
    color: #002a66;
    margin-top: 4rem;
}
.popular-category-section {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 2rem;
    padding: 2rem;
    background: #f0f6ff;
    max-width: 1100px;
    margin: auto;
    border-radius: 12px;
}
.popular-category {
    background: linear-gradient(135deg, #004aad, #002a66);
    color: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    text-align: center;
    transition: transform 0.3s ease;
    font-size: 1.1rem;
}
.popular-category:hover {
    transform: scale(1.05);
}
.filter-sort-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 2rem;
    flex-wrap: wrap;
    background: #fff;
    margin: 2rem auto;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08);
    max-width: 1100px;
}
.filter-sort-section::before {
    content: 'Refine your course exploration with filters and sorting options';
    width: 100%;
    font-size: 1.1rem;
    color: #002a66;
    margin-bottom: 1rem;
    display: block;
    text-align: center;
    font-weight: 500;
}
.filter-sort-section select, .filter-sort-section button {
    padding: 0.7rem 1.2rem;
    border: 1px solid #ccc;
    border-radius: 10px;
    font-size: 1rem;
    margin: 0.5rem;
    background: #e6f0ff;
    color: #004aad;
    font-weight: 600;
    transition: 0.2s ease;
}
.filter-sort-section button:hover {
    background: #004aad;
    color: #fff;
}
.course-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2rem;
    padding: 3rem;
    background: #f8fbff;
}
.course-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
}
.course-card:hover {
    transform: translateY(-6px);
}
.course-card img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
.course-card-content {
    padding: 1.5rem;
    flex: 1;
}
.course-card h3 {
    color: #002a66;
    font-size: 1.3rem;
    margin: 0;
}
.course-card .meta {
    font-size: 0.9rem;
    color: #555;
    margin-top: 0.5rem;
}
.course-card .price {
    font-weight: bold;
    color: #004aad;
    margin-top: 1rem;
    font-size: 1rem;
}
.course-card .preview-link {
    background: #004aad;
    color: white;
    padding: 0.9rem;
    text-align: center;
    text-decoration: none;
    border-radius: 0 0 14px 14px;
    font-weight: bold;
    transition: background 0.3s ease;
}
.course-card .preview-link:hover {
    background: #002a66;
}

</style>
<style>
.course-category-group {
    margin-bottom: 3rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f9f9f9;
    position: relative;
}
.course-category-group h3 {
    font-size: 1.8rem;
    color: #004aad;
    margin-bottom: 1rem;
}
.course-grid {
    display: grid;
    gap: 1.5rem;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
}
.course-card {
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.07);
    transition: transform 0.3s ease;
    display: flex;
    flex-direction: column;
}
.course-card:hover {
    transform: translateY(-5px);
}
.course-card img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}
.course-card-content {
    padding: 1rem;
    flex-grow: 1;
}
.course-card-content h3 {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}
.course-card-content .meta {
    font-size: 0.9rem;
    color: #666;
    margin-bottom: 0.5rem;
}
.course-card-content .price {
    font-weight: bold;
    color: #004aad;
}
.preview-link {
    display: block;
    text-align: center;
    background: #004aad;
    color: white;
    padding: 0.7rem;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.3s ease;
}
.preview-link:hover {
    background: #002e7d;
}
.toggle-btn {
    position: absolute;
    top: 1.5rem;
    right: 1.5rem;
    background: #004aad;
    color: white;
    padding: 0.3rem 0.8rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
}
.cat-name{
    margin-left:3em;
}
</style>
<div class="hero-section">
    <h1>Explore Our Courses</h1>
    <p>Browse by category, search instantly, and find what's trending in our learning community.</p>
    <div class="search-bar">
        <form method="get" id="live-search" onsubmit="return false;">
            <input type="text" id="search-input" placeholder="Search courses like 'Intro to Design'...">
            <div id="search-suggestions"></div>
        </form>
    </div>
</div>


<h2 class="section-title">Popular Categories</h2>
<div class="popular-category-section">
    <?php foreach ($top_categories as $cat): ?>
        <div class="popular-category">
            <?php echo esc_html($cat['name']); ?> (<?php echo $cat['count']; ?> learners)
        </div>
    <?php endforeach; ?>
</div>

<h2 class="section-title">Filter & Sort Courses</h2>
<div class="filter-sort-section">
    <form method="get">
        <select name="category" onchange="this.form.submit()">
            <option value="">Filter by Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat->term_id; ?>" <?php selected($selected_cat, $cat->term_id); ?>><?php echo $cat->name; ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="order" value="price">
            <i class="fas fa-sort-amount-up"></i> Sort by Price
        </button>
    </form>
</div>

<?php
$categories = get_terms([
    'taxonomy' => 'course_category',
    'hide_empty' => true,
]);



$filter_applied = ($search_term || $selected_cat || $order_by_price);

// If filter is applied or on initial load, show course grid
if (!$filter_applied) {
    $courses = get_posts([
        'post_type' => 'course',
        'posts_per_page' => -1,
    ]);
}
?>

<?php if (!empty($courses)): ?>
    <h2 class="section-title"><?php echo $filter_applied ? 'Filtered Courses' : 'All Courses'; ?></h2>

    <?php
    // Group courses by category
    $grouped = [];

    foreach ($courses as $course) {
        $terms = get_the_terms($course->ID, 'course_category');
        if (!empty($terms)) {
            foreach ($terms as $term) {
                $grouped[$term->term_id]['category'] = $term;
                $grouped[$term->term_id]['courses'][] = $course;
            }
        } else {
            $grouped[0]['category'] = (object)[ 'name' => 'Uncategorized', 'term_id' => 0 ];
            $grouped[0]['courses'][] = $course;
        }
    }

    // Index for toggle IDs
    $toggleIndex = 0;
    foreach ($grouped as $group):
        $category = $group['category'];
        $courseList = $group['courses'];
        if (empty($courseList)) continue;
    ?>
    <div class="course-category-group">
        <h3 class="cat-name"><?php echo esc_html($category->name); ?></h3>
        <button class="toggle-btn" onclick="toggleCourses('group-<?php echo $toggleIndex; ?>')">Toggle</button>
        <div class="course-grid" id="group-<?php echo $toggleIndex; ?>">
            <?php foreach ($courseList as $course):
                $thumb = get_the_post_thumbnail_url($course->ID, 'medium') ?: get_template_directory_uri() . '/assets/images/course-placeholder.jpg';
                $price = get_post_meta($course->ID, 'price', true);
            ?>
            <div class="course-card">
                <img src="<?php echo esc_url($thumb); ?>" alt="Course thumbnail">
                <div class="course-card-content">
                    <h3><?php echo esc_html($course->post_title); ?></h3>
                    <div class="meta"><?php echo strip_tags(get_the_term_list($course->ID, 'course_category', '', ', ')); ?></div>
                    <div class="price">$<?php echo number_format((float) $price, 2); ?></div>
                </div>
                <a class="preview-link" href="<?php echo get_permalink($course->ID); ?>">Preview Course</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php $toggleIndex++; endforeach; ?>

<?php else: ?>
    <h2 class="section-title">Courses</h2>
    <p style="text-align: center;">No courses found.</p>
<?php endif; ?>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('search-input');
    const suggestions = document.getElementById('search-suggestions');

    input.addEventListener('input', function () {
        const query = input.value.trim();
        if (query.length < 2) {
            suggestions.style.display = 'none';
            suggestions.innerHTML = '';
            return;
        }

        fetch('<?php echo admin_url('admin-ajax.php'); ?>?action=live_course_search&q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length) {
                    data.forEach(item => {
                        const link = document.createElement('a');
                        link.href = item.link;
                        link.textContent = item.title;
                        suggestions.appendChild(link);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            })
            .catch(err => console.error('Search error:', err));
    });

    // Optional: hide suggestions when clicking outside
    document.addEventListener('click', function (e) {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});
</script>

<script>
function toggleCourses(id) {
    const grid = document.getElementById(id);
    grid.style.display = grid.style.display === 'none' ? 'grid' : 'none';
}
</script>
<style>
.search-bar {
    position: relative;
    max-width: 600px;
    margin: 0 auto;
}


#search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ccc;
    border-top: none;
    max-height: 300px;
    overflow-y: auto;
    z-index: 999;
    display: none;
}

#search-suggestions a {
    display: block;
    padding: 0.75rem 1rem;
    text-decoration: none;
    color: #004aad;
    border-bottom: 1px solid #eee;
}

#search-suggestions a:hover {
    background-color: #f0f8ff;
}
</style>
<?php get_footer(); ?>
