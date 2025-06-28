<?php
/* Template Name: Instructor Create */
get_header();

if (!is_user_logged_in()) {
    wp_redirect(wp_login_url(get_permalink()));
    exit;
}

$current_user = wp_get_current_user();
if (!in_array('instructor', $current_user->roles)) {
    echo "<p style='text-align:center;color:red;'>Access Denied: Instructors Only.</p>";
    get_footer();
    exit;
}
?>

<style>
  .instructor-dashboard {
    max-width: 900px;
    margin: 2rem auto;
    padding: 2rem;
    background: #f0f8ff;
    border-radius: 8px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }
  .instructor-dashboard h2 {
    color: #004aad;
    margin-bottom: 1rem;
    border-bottom: 2px solid #004aad;
    padding-bottom: 0.5rem;
  }
  form label {
    display: block;
    margin-top: 1rem;
    font-weight: 600;
    color: #002a66;
  }
  form input[type="text"],
  form input[type="number"],
  form select,
  form textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1.5px solid #004aad;
    border-radius: 6px;
    font-size: 1rem;
    margin-top: 0.3rem;
    box-sizing: border-box;
    transition: border-color 0.3s;
  }
  form input[type="text"]:focus,
  form input[type="number"]:focus,
  form select:focus,
  form textarea:focus {
    border-color: #0066ff;
    outline: none;
    box-shadow: 0 0 8px #0066ff88;
  }
  input[type="file"] {
    margin-top: 0.5rem;
  }
  button, .btn {
    background: #004aad;
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 6px;
    margin-top: 1.5rem;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
  }
  button:hover, .btn:hover {
    background: #003b7a;
  }
  .module {
    background: #d9e6ff;
    padding: 15px;
    border-radius: 8px;
    margin-top: 1rem;
    position: relative;
  }
  .module label {
    margin-top: 0;
  }
  .module .remove-module {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #b30000;
    border: none;
    color: white;
    font-weight: bold;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    line-height: 22px;
    text-align: center;
  }
  .course-list {
    margin-top: 2rem;
    width: 100%;
    border-collapse: collapse;
  }
  .course-list th, .course-list td {
    border: 1px solid #ddd;
    padding: 8px;
  }
  .course-list th {
    background: #e6f0ff;
    color: #002a66;
    text-align: left;
  }
  .course-actions button {
    margin-right: 0.5rem;
    background: #0077cc;
    border: none;
    color: white;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9rem;
  }
  .course-actions button.delete {
    background: #cc0000;
  }
  .ajax-feedback {
    margin-top: 1rem;
    font-weight: 600;
  }
  .error-message {
    color: #b30000;
    margin-top: 0.5rem;
  }
</style>

<div class="instructor-dashboard">
  <h2>Create / Edit Course</h2>

  <form id="course-form" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="course_id" value="">
    <?php wp_nonce_field('course_action','course_nonce'); ?>

    <label for="course_title">Title *</label>
    <input id="course_title" type="text" name="course_title" placeholder="Course Title" required>

    <label for="course_desc">Description *</label>
    <textarea id="course_desc" name="course_desc" rows="4" placeholder="Brief course description" required></textarea>

    <label for="course_category">Category *</label>
    <select id="course_category" name="course_category" required>
      <option value="" disabled selected>Select category</option>
      <?php 
      $terms = get_terms(['taxonomy' => 'course_category', 'hide_empty' => false]);
      if (!is_wp_error($terms)) {
        foreach ($terms as $t) {
          echo '<option value="'.esc_attr($t->term_id).'">'.esc_html($t->name).'</option>';
        }
      }
      ?>
    </select>

    <label for="course_price">Price</label>
    <input id="course_price" type="number" name="course_price" step="0.01" min="0" placeholder="0.00">

    <label for="course_thumbnail">Thumbnail</label>
    <input id="course_thumbnail" type="file" name="course_thumbnail" accept="image/*">

    <div id="modules-wrapper">
      <label>Modules *</label>
      <div class="module" data-index="0">
        <button type="button" class="remove-module" title="Remove Module" style="display:none;">&times;</button>
        <label>Module Title *</label>
        <input type="text" name="modules[0][title]" placeholder="Module Title" required>

        <label>Module Description</label>
        <textarea name="modules[0][desc]" placeholder="Module Description" rows="2"></textarea>

        <label>Attachment</label>
        <input type="file" name="modules[0][attachment]" accept="*/*">
      </div>
    </div>

    <button type="button" id="add-module" class="btn">+ Add Module</button>

    <button type="submit" class="btn">Save Course</button>
    <div class="ajax-feedback"></div>
  </form>

  <h2>Your Courses</h2>
  <table class="course-list" aria-live="polite" aria-relevant="all" aria-atomic="true">
    <thead>
      <tr><th>Title</th><th>Status</th><th>Actions</th></tr>
    </thead>
    <tbody></tbody>
  </table>
</div>

<script>
jQuery(function($){
  let moduleIndex = 1;

  function showFeedback(message, isError = false){
    $('.ajax-feedback').text(message).css('color', isError ? '#b30000' : '#007700');
  }

  function resetForm(){
    $('#course-form')[0].reset();
    $('#modules-wrapper').html(`
      <div class="module" data-index="0">
        <button type="button" class="remove-module" title="Remove Module" style="display:none;">&times;</button>
        <label>Module Title *</label>
        <input type="text" name="modules[0][title]" placeholder="Module Title" required>
        <label>Module Description</label>
        <textarea name="modules[0][desc]" placeholder="Module Description" rows="2"></textarea>
        <label>Attachment</label>
        <input type="file" name="modules[0][attachment]" accept="*/*">
      </div>`);
    moduleIndex = 1;
  }

  function loadCourses(){
    $.post(ajaxurl, { action:'load_instructor_courses' }, function(res){
      if(!Array.isArray(res)) return;
      const tbody = $('.course-list tbody').empty();
      if(res.length === 0){
        tbody.append('<tr><td colspan="3" style="text-align:center;color:#777;">No courses found.</td></tr>');
        return;
      }
      res.forEach(c => {
        tbody.append(`
          <tr data-id="${c.id}">
            <td>${c.title}</td>
            <td>${c.status}</td>
            <td class="course-actions">
              <button class="edit" aria-label="Edit course ${c.title}">Edit</button>
              <button class="delete" aria-label="Delete course ${c.title}">Delete</button>
            </td>
          </tr>
        `);
      });
    });
  }

  // Load courses on page ready
  loadCourses();

  // Add new module UI
  $('#add-module').on('click', function(){
    $('#modules-wrapper').append(`
      <div class="module" data-index="${moduleIndex}">
        <button type="button" class="remove-module" title="Remove Module">&times;</button>
        <label>Module Title *</label>
        <input type="text" name="modules[${moduleIndex}][title]" placeholder="Module Title" required>
        <label>Module Description</label>
        <textarea name="modules[${moduleIndex}][desc]" placeholder="Module Description" rows="2"></textarea>
        <label>Attachment</label>
        <input type="file" name="modules[${moduleIndex}][attachment]" accept="*/*">
      </div>
    `);
    moduleIndex++;
  });

  // Remove module handler
  $('#modules-wrapper').on('click', '.remove-module', function(){
    $(this).closest('.module').remove();
  });

  // Form submit with validation
  $('#course-form').on('submit', function(e){
    e.preventDefault();

    // Basic validation: check if all module titles filled
    let valid = true;
    $('#modules-wrapper .module').each(function(){
      const title = $(this).find('input[type=text]').val().trim();
      if(!title){
        alert('Please fill all module titles.');
        valid = false;
        return false; // break each loop
      }
    });
    if(!valid) return;

    let data = new FormData(this);
    $.ajax({
      url: ajaxurl + '?action=save_instructor_course',
      type: 'POST',
      data: data,
      contentType: false,
      processData: false,
      success: function(res){
        if(res.success){
          showFeedback(res.message);
          resetForm();
          loadCourses();
        } else {
          showFeedback(res.message || 'Error saving course', true);
        }
      },
      error: function(){
        showFeedback('AJAX error: Could not save course', true);
      }
    });
  });

  // Edit course handler
  $('.course-list').on('click', '.edit', function(){
    const id = $(this).closest('tr').data('id');
    $.post(ajaxurl, { action: 'get_instructor_course', course_id: id }, function(c){
      if(!c || !c.id){
        showFeedback('Course data not found.', true);
        return;
      }
      $('input[name=course_id]').val(c.id);
      $('input[name=course_title]').val(c.title);
      $('textarea[name=course_desc]').val(c.desc);
      $('select[name=course_category]').val(c.category);
      $('input[name=course_price]').val(c.price);
      $('#modules-wrapper').empty();
      c.modules.forEach((m, i) => {
        $('#modules-wrapper').append(`
          <div class="module" data-index="${i}">
            <button type="button" class="remove-module" title="Remove Module">&times;</button>
            <label>Module Title *</label>
            <input type="text" name="modules[${i}][title]" value="${m.title}" required>
            <label>Module Description</label>
            <textarea name="modules[${i}][desc]" rows="2">${m.desc}</textarea>
            <label>Attachment</label>
            <input type="file" name="modules[${i}][attachment]" accept="*/*">
          </div>
        `);
        moduleIndex = i + 1;
      });
    });
  });

  // Delete course handler
  $('.course-list').on('click', '.delete', function(){
    if(!confirm('Are you sure you want to delete this course?')) return;
    const id = $(this).closest('tr').data('id');
    $.post(ajaxurl, { action:'delete_instructor_course', course_id: id }, function(res){
      alert(res.message);
      loadCourses();
    });
  });
});
</script>

<?php get_footer(); ?>
