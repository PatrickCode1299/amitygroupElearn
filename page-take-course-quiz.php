<?php
/**
 * Template Name: Take Course Quiz
 */

get_header();

$quiz_name = isset($_GET['quiz_slug']) ? sanitize_title($_GET['quiz_slug']) : '';
$course_id = isset($_GET['cid']) ? intval($_GET['cid']) : 0;

if (!$quiz_name || !$course_id) {
    echo '<p style="color:red;">Missing quiz parameters.</p>';
    get_footer();
    return;
}

echo '<div class="quiz-container">';
echo '<h2>Quiz for: ' . esc_html(ucwords(str_replace('-', ' ', $quiz_name))) . '</h2>';

$course_sections = gdlr_lms_get_course_content_settings($course_id);
$quiz_post_id = null;
$matched_duration = 5;
$quiz_questions = [];

foreach ($course_sections as $section) {
    $section_slug = sanitize_title($section['section-name'] ?? '');

    if ($section_slug === $quiz_name && !empty($section['section-quiz']) && $section['section-quiz'] !== 'none') {
        $quiz_post_id = intval($section['section-quiz']);
        $matched_duration = !empty($section['wait-time']) ? intval($section['wait-time']) : 5;

        // Get the quiz content JSON string
        $section_json = get_post_meta($quiz_post_id, 'gdlr-lms-content-settings', true);
        $section_data = json_decode($section_json, true);

        if (!empty($section_data) && isset($section_data[0]['question'])) {
            $raw_question_data = $section_data[0]['question'];

            if (is_string($raw_question_data)) {
                // Replace delimiter with real double quotes
                $decoded_string = str_replace('|gq2|', '"', $raw_question_data);
                $questions = json_decode($decoded_string, true);

                if (!empty($questions) && is_array($questions)) {
                    $quiz_questions = $questions;
                }
            }
        }
        break;
    }
}

if ($quiz_post_id) {
    echo '<p>This quiz must be completed in <strong>' . esc_html($matched_duration) . ' minutes</strong>.</p>';

    if (!empty($quiz_questions)) :
        ?>
        <div class="quiz-timer-wrapper">
            <div id="quiz-timer" class="quiz-timer" data-mins="<?php echo esc_attr($matched_duration); ?>">
                Time left: <span id="countdown"><?php echo sprintf('%02d:00', $matched_duration); ?></span>
            </div>

            <form method="post" id="quiz-form">
                <?php
                $q_num = 1;
                foreach ($quiz_questions as $q) :
                    if (empty($q['question']) || empty($q['quiz-choice'])) continue;
                    ?>
                    <div class="quiz-question">
                        <p><strong><?php echo $q_num . '. ' . esc_html($q['question']); ?></strong></p>
                        <?php foreach ($q['quiz-choice'] as $i => $choice) : ?>
                            <label>
                                <input type="radio" name="q<?php echo $q_num; ?>" value="<?php echo esc_attr($choice); ?>">
                                <?php echo esc_html($choice); ?>
                            </label><br>
                        <?php endforeach; ?>
                    </div>
                    <?php
                    $q_num++;
                endforeach;
                ?>
                <button type="submit">Submit</button>
            </form>
        </div>
        <?php
    else :
        echo '<p style="color:red;">✅ Quiz found but no questions decoded. This usually means question JSON is malformed or not properly parsed.</p>';
    endif;
} else {
    echo '<p>No quiz found for this module.</p>';
}

echo '</div>';
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const timerElement = document.getElementById('quiz-timer');
    if (!timerElement) return;

    const durationInMinutes = parseInt(timerElement.dataset.mins || "5", 10);
    let duration = durationInMinutes * 60;
    const display = document.getElementById('countdown');
    const form = document.getElementById('quiz-form');

    let timer = setInterval(function () {
        let minutes = String(Math.floor(duration / 60)).padStart(2, '0');
        let seconds = String(duration % 60).padStart(2, '0');
        display.textContent = `${minutes}:${seconds}`;

        if (--duration < 0) {
            clearInterval(timer);
            alert('⏰ Time is up! Submitting your quiz...');
            if (form) form.submit();
        }
    }, 1000);
});
</script>

<style>
.quiz-container {
  max-width: 700px;
  margin: 2rem auto;
  padding: 2rem;
  background: #f9f9ff;
  border-radius: 10px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.quiz-container h2 {
  text-align: center;
  margin-bottom: 1rem;
  font-size: 1.8rem;
  color: #002a66;
}
.quiz-question {
  margin-bottom: 1.5rem;
}
.quiz-question p {
  font-weight: bold;
}
.quiz-question label {
  display: block;
  margin-bottom: 0.3rem;
}
.quiz-question input[type="radio"] {
  margin-right: 8px;
}
.quiz-timer {
  background: #002a66;
  color: #fff;
  font-weight: bold;
  padding: 0.7rem 1.2rem;
  border-radius: 8px;
  text-align: center;
  font-size: 1.2rem;
  max-width: 200px;
  margin: 1rem auto 1.5rem;
}
.quiz-container button {
  background: #004aad;
  color: white;
  padding: 0.6rem 1.4rem;
  font-weight: bold;
  font-size: 1rem;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: block;
  margin: 1.5rem auto 0;
}
.quiz-container button:hover {
  background: #003080;
}
</style>

<?php get_footer(); ?>
