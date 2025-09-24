<?php
/* Template Name: All Users */

if (!is_user_logged_in() || !current_user_can('administrator')) {
    wp_redirect(site_url('/')); // redirect non-admins
    exit;
}

get_header();

// Pagination setup
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$per_page = 10;

// Get users ordered by registration date (latest first)
$args = [
    'orderby'     => 'registered',
    'order'       => 'DESC',
    'number'      => $per_page,
    'offset'      => ($paged - 1) * $per_page,
];
$users = get_users($args);
$total_users = count_users();
$total_users = $total_users['total_users'];
$total_pages = ceil($total_users / $per_page);

// Serial number start
$serial = ($paged - 1) * $per_page + 1;
?>

<style>
.users-table-wrapper {
    width: 100%;
    overflow-x: auto; /* responsive scroll */
}
.users-table {
    width: 100%;
    border-collapse: collapse;
    margin: 2rem 0;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    min-width: 600px; /* ensures structure before wrap */
}
.users-table th, .users-table td {
    padding: 12px 16px;
    border-bottom: 1px solid #eee;
    text-align: left;
    font-size: 0.95rem;
    white-space: nowrap; /* prevents breaking */
}
.users-table th {
    background: #004aad;
    color: white;
    font-weight: bold;
}
.users-table tr:hover {
    background: #f9fbff;
}
.page-header {
    text-align: center;
    margin: 2rem 0;
}
.page-header h2 {
    color: #ffffffff;
    font-size: 2rem;
}
.pagination {
    text-align: center;
    margin: 1.5rem 0;
}
.pagination a,
.pagination span {
    display: inline-block;
    margin: 0 5px;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ddd;
    text-decoration: none;
    color: #004aad;
    font-size: 0.95rem;
}
.pagination .current {
    background: #004aad;
    color: white;
    border-color: #004aad;
}
.pagination a:hover {
    background: #f1f5ff;
}
</style>

<div class="page-header">
    <h2>All Users</h2>
</div>

<div class="users-table-wrapper">
    <table class="users-table">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Role</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $serial++; ?></td>
                        <td><?= esc_html($user->user_login) ?></td>
                        <td><?= esc_html($user->user_email) ?></td>
                        <td><?= esc_html(get_user_meta($user->ID, 'phone_number', true)) ?></td>
                        <td><?= implode(', ', $user->roles) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No users found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="pagination">
    <?php
    echo paginate_links([
        'total'   => $total_pages,
        'current' => $paged,
        'format'  => '?paged=%#%',
        'show_all'=> false,
        'type'    => 'plain',
        'prev_text' => '&laquo; Prev',
        'next_text' => 'Next &raquo;',
    ]);
    ?>
</div>

<?php get_footer(); ?>
