<?php
/**
 * Include necessary files
 */
function qa_poll_init() {
  require_once get_template_directory() . '/includes/poll/poll-settings.php';
  require_once get_template_directory() . '/includes/poll/poll-posttype.php';
  require_once get_template_directory() . '/includes/poll/poll-action.php';
  require_once get_template_directory() . '/includes/poll/poll-template.php';

    // Init poll actions
  $poll_actions = QA_Poll_Actions::get_instance();
  $poll_actions->init();

    // Define variables
  define('POLL_MAX_ANSWER', ae_get_option('poll_max_answer', 5));
  define('POLL_MAX_ANSWER_ERROR_TEXT', sprintf(__('You can only create %s answers.', ET_DOMAIN), POLL_MAX_ANSWER));
  define('POLL_CHART_TYPE', ae_get_option('poll_chart_type', 'pie_chart'));
  define('POLL_CHECK_USER_VOTED', ae_get_option('poll_checked_user_voted', 'ip'));
}

add_action('after_setup_theme', 'qa_poll_init');

function qa_poll_posttype_init() {
    // Init posttype
  $poll_question = QA_Poll_Questions_Posttype::get_instance();
  $poll_question->init();

  $poll_answer = QA_Poll_Answers_Posttype::get_instance();
  $poll_answer->init();
}

add_action('init', 'qa_poll_posttype_init', 15);

if(!function_exists('qa_init_poll_scripts')) {
  /**
   * Add poll scripts and styles
   * @param void
   * @return void
   * @since 2.0
   * @author tatthien
   */
  function qa_init_poll_scripts() {
    global $current_user, $post;
    wp_enqueue_style('wp-color-picker');

    wp_enqueue_script('wp-color-picker');
    wp_enqueue_script('poll', get_template_directory_uri() . '/js/poll.js', array(
      'jquery',
      'underscore',
      'backbone',
      'appengine',
      'iris'
      ), '1.0', true);

    // Localize variables for poll
    if(!empty($post)) {
      $is_voted = qa_poll_check_user_vote($post->ID);
    } else {
      $is_voted = false;
    }

    wp_localize_script('poll', 'poll_settings', array(
      'max_answer' => POLL_MAX_ANSWER,
      'max_answer_error_text' => POLL_MAX_ANSWER_ERROR_TEXT,
      'answer_placeholder' => __('Your answer', ET_DOMAIN),
      'poll_chart_type' => POLL_CHART_TYPE,
      'user_voted' => $is_voted
      ));

    if(is_singular('poll')) {
      wp_enqueue_script('poll-resize', get_template_directory_uri() . '/js/throttledresize.js', array(
        'jquery',
        'underscore',
        'backbone',
        'appengine',
        ), '1.0', true);

          // Google chart
      wp_enqueue_script('google-chard', "https://www.google.com/jsapi?autoload=
        {'modules':[{'name':'visualization','version':'1.1','packages':
        ['corechart']}]}", array(), '1.0', true);

          // Init chart for single poll
      wp_enqueue_script('poll-chart', get_template_directory_uri() . '/js/poll-chart.js', array(
        'jquery',
        'underscore',
        'backbone',
        'appengine',
        ), '1.0', true);
    }
  }

  add_action('wp_enqueue_scripts', 'qa_init_poll_scripts');
}

if(!function_exists('qa_poll_chart_styles')) {
  /**
   * Add styles for chart select in setting
   * @param void
   * @return void
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_chart_styles() {
    if(isset($_GET['page']) && $_GET['page'] == 'et-poll'){
      ?>
      <style>
        #poll_chart_type .form-radio-item {
          width: 25%;
          float: left;
          text-align: center;
        }

        #poll_chart_type .form-radio-item label {
          position: relative;
        }

        #poll_chart_type .form-radio-item label input {
          position: absolute;
          bottom: -10px;
          left: 50%;
          margin-left: -10px;
        }

        #poll_chart_type .form-radio-item img {
          max-width: 100%;
        }
      </style>
      <?php
    }
  }

  add_action('admin_head', 'qa_poll_chart_styles');
}

if(!function_exists('qa_poll_footer_hook')) {
  /**
   * Function hook data to footer
   * @param void
   * @return void
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_footer_hook() {
    global $post;

      // Add javascript variables for edit poll and show chart
    if(is_singular('poll')) {
          // Get answer color, votes for Google chart
      $answers = qa_get_poll_answers($post->ID);

          // Declare default arrays
      $chart_slices = array();
      $chart_slices_value = array();

          // Sort the answers
      $answers = qa_poll_sort_answers($answers);

          // Fill arrays with data
      foreach($answers as $answer) {
        $chart_slices[]['color']  = $answer->poll_answer_color;
        $chart_slices_value[] = array(
          'name' => $answer->post_title,
          'vote' => qa_poll_get_answer_vote($answer->ID)
          );
      }
      $pollAnswers = qa_get_poll_answers($post->ID);
      ?>
      <!-- Encode data to json -->
      <script type="text/javascript" id="poll_answers_json">
        pollAnswers = <?php echo json_encode($pollAnswers); ?>;
        chartSlices = <?php echo json_encode($chart_slices); ?>;
        chartSlicesValue = <?php echo json_encode($chart_slices_value); ?>;
      </script>
      <?php
    }
  }

  add_action('wp_footer', 'qa_poll_footer_hook');
}

if(!function_exists('qa_poll_filter')) {
  /**
   * Filter question list by poll, question, all
   * @param void
   * @return array post_type
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_filter() {
    if(isset($_GET['qtype'])) {
      if($_GET['qtype'] == 'all') {
        return array('question', 'poll');
      } else if($_GET['qtype'] == 'poll') {
        return array('poll');
      } else if($_GET['qtype'] == 'normal') {
        return array('question');
      } else {
        return array('question', 'poll');
      }
    } else {
      return array('question', 'poll');
    }
  }
}

if(!function_exists('qa_get_poll_answers')) {
  /**
   * Get poll answers
   * @param int $post_author
   * @param int $post_parent
   * @return object $results
   * @since 2.0
   * @author tatthien
   */
  function qa_get_poll_answers($post_parent) {
    global $ae_post_factory;
    $args = array(
      'post_type' => 'poll_answer',
      'posts_per_page' => -1,
      'post_parent' => $post_parent,
      'post_status' => 'publish',
      'orderby' => 'date',
      'order' => 'ASC',
      );

    $answers = get_posts($args);
    $results = array();
    $post = $ae_post_factory->get('poll_answer');
    foreach($answers as $answer) {
      $results[] = $post->convert($answer);
    }

    return $results;
  }
}

if(!function_exists('qa_poll_get_total_vote')) {
  /**
   * Get total votes of a poll question
   * @param int $poll_id
   * @return int $total_votes
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_get_total_vote($poll_id) {
    $answers = qa_get_poll_answers($poll_id);
    $total_votes = 0;
    foreach($answers as $answer) {
      $comment = wp_count_comments($answer->ID);
      $total_votes += $comment->total_comments;
    }

    return $total_votes;
  }
}

if(!function_exists('qa_poll_get_answer_vote')) {
  /**
   * Get answer vote by get comment count
   * @param $answer_id
   * @return int $vote
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_get_answer_vote($answer_id) {
    $vote = 0;
    $comment = wp_count_comments($answer_id);
    $vote = $comment->total_comments;
    return $vote;
  }
}

if(!function_exists('qa_poll_sort_answers')) {
  /**
   * Sort answers by votes. Return object will be sort by the highest votes of each answer.
   * @param object $answer
   * @return $object $answer
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_sort_answers($answers) {
      // Sort the answers have higtest votes
    usort($answers, 'sort_by_vote');
    return $answers;
  }

   /**
    * Function compare two anwswers with their votes.
    * @param object $a
    * @param object $b
    * @return int
    * @since 2.0
    * @author tatthien
    */
   function sort_by_vote($a, $b) {
    if((int)$a->poll_answer_vote == (int)$b->poll_answer_vote) {
      return 0;
    }
    return ((int)$a->poll_answer_vote > (int)$b->poll_answer_vote) ? -1 : 1;
  }
}

if(!function_exists('qa_poll_check_user_vote')) {
  /**
   * Check if user has voted or not
   * @param int $poll_id
   * @return boolean
   * @since 2.0
   * @author tatthien
   */
  function qa_poll_check_user_vote($poll_id) {
    global $current_user;

    // Check poll expired
    if(qa_poll_check_end_date($poll_id)) {
      return true;
    } else {
      // Check vote by IP
      if(POLL_CHECK_USER_VOTED == 'ip') {
        $comments = get_comments(array(
          'post_id' => $poll_id,
          'type' => 'submit_poll_question',
          'author_email' => $current_user->user_email,
          'number' => 1
          ));

        if(!empty($comments)) {
          return true;
        }else {
          return false;
        }
      } else { // Check vote by cookie
        if($current_user->ID) { // If is author
          $comments = get_comments(array(
            'post_id' => $poll_id,
            'type' => 'submit_poll_question',
            'author_email' => $current_user->user_email,
            'number' => 1
            ));

          if(!empty($comments)) {
            return true;
          }else {
            return false;
          }
        } else { // If is guest
          // Check cookie if user is guest (no loggin)
          if(isset($_COOKIE["poll_id_$poll_id"]) && $poll_id == $_COOKIE["poll_id_$poll_id"]) {
            return true;
          } else {
            return false;
          }
        }
      }
    }
  }
}

if(!function_exists('qa_poll_check_end_date')) {
  /**
   * Check if the poll is expired or not
   * @param int $poll_id
   * @return boolean
   * @since 2.0
   * @author tatthie
   */
  function qa_poll_check_end_date($poll_id) {
    // Convert date to timestamp

    $poll_end_date = get_post_meta($poll_id, 'poll_end_date', true);
    if(!empty($poll_end_date)) {
      $end_time =  strtotime(DateTime::createFromFormat('d/m/Y', $poll_end_date)->format('m/d/Y'));
      $current_time = strtotime(DateTime::createFromFormat('d/m/Y', date('d/m/Y', time()))->format('m/d/Y'));

      // Compare current timestamp with end-date timestamp
      if($current_time >= $end_time) {
        return true;
      } else {
        return false;
      }
    }
  }
}