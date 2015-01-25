<?php
/*
Plugin Name: Train-up! Orderable questions
Description: Addon that enables a new type of question, for which trainees are required to choose the correct order from a pre-defined list of things.
Version: 0.0.2
License: GPL2
*/

namespace TU;

class Orderable_questions_addon {

  /**
   * __construct
   *
   * Listen to the filters that the Train-Up! plugin provides, and latch on,
   * inserting the new functionality where needed.
   *
   * @access public
   */
  public function __construct() {
    $this->path = plugin_dir_path(__FILE__);
    $this->url  = plugin_dir_url(__FILE__);

    add_action('init', array($this, '_register_assets'));
    add_filter('tu_question_types', array($this, '_add_type'));
    add_filter('tu_question_meta_boxes', array($this, '_add_meta_box'), 10, 2);
    add_action('tu_meta_box_orderable', array($this, '_meta_box'));
    add_action('tu_save_question_orderable', array($this, '_save_question'));
    add_action('tu_question_backend_assets', array($this, '_add_backend_assets'));
    add_action('tu_question_frontend_assets', array($this, '_add_frontend_assets'));
    add_filter('tu_render_answers_orderable', array($this, '_render_answers'), 10, 3);
    add_filter('tu_validate_answer_orderable', array($this, '_validate_answer'), 10, 3);
  }

  /**
   * _register_assets
   *
   * - Fired on `init`
   * - Register the scripts and styles for the front end backend orderable
   *   questions add-on.
   *
   * @access public
   */
  public function _register_assets() {
    wp_register_script('tu_orderable_questions', "{$this->url}js/backend/orderable_questions.js", array('jquery-ui-sortable'));
    wp_register_style('tu_orderable_questions', "{$this->url}css/backend/orderable_questions.css");
    wp_register_script('tu_frontend_orderable_questions', "{$this->url}js/frontend/orderable_questions.js", array('jquery-ui-sortable'));
    wp_register_style('tu_frontend_orderable_questions', "{$this->url}css/frontend/orderable_questions.css");
  }

  /**
   * _add_type
   *
   * - Callback for when retrieving the hash of question types.
   * - Insert our new 'orderable' question type.
   *
   * @param mixed $types
   *
   * @access public
   *
   * @return array The altered types
   */
  public function _add_type($types) {
    $types['orderable'] = __('Orderable', 'trainup');

    return $types;
  }

  /**
   * _add_meta_box
   *
   * - Callback for when the meta boxes are defined for Question admin screens
   * - Define one for our custom Question type: orderable
   *
   * @param mixed $meta_boxes
   *
   * @access public
   *
   * @return array The altered meta boxes
   */
  public function _add_meta_box($meta_boxes) {
    $meta_boxes['orderable'] = array(
      'title'    => __('Orderable options', 'trainup'),
      'context'  => 'advanced',
      'priority' => 'default'
    );

    return $meta_boxes;
  }

  /**
   * _meta_box
   *
   * - Callback function for an action that is fired when the 'orderable' meta
   *   box is to be rendered.
   * - Echo out the view that lets the administrator choose the correct order
   *   of the items for the question.
   *
   * @access public
   */
  public function _meta_box() {
    echo new View(tu()->get_path('/view/backend/questions/multiple_answers_meta'), array(
      'id'       => 'answer_orderable_template',
      'question' => tu()->question,
      'answers'  => tu()->question->answers
    ));
  }

  /**
   * _save_question
   *
   * - Fired when an orderable question is saved
   * - Update the Question's correct answer so that is can be validated.
   *
   * @param mixed $question
   *
   * @access public
   */
  public function _save_question($question) {
    if (
      !isset($_POST['multiple_answer'])   ||
      count($_POST['multiple_answer']) < 2
    ) {
      wp_die(__('Orderable questions require at least two answers', 'trainup'));
    }

    update_post_meta($question->ID, 'tu_answers', $_POST['multiple_answer']);
  }

  /**
   * _add_backend_assets
   *
   * - Fired when styles and scripts are enqueued in the backend
   * - Enqueue the script that helps the user create their orderable question.
   * - Enqueue the style that adds the 'move' cursor to the orderable items.
   * - jQuery UI Sortable script gets enqueued automatically as a dependency
   *
   * @access public
   */
  public function _add_backend_assets() {
    wp_enqueue_script('tu_orderable_questions');
    wp_enqueue_style('tu_orderable_questions');
  }

  /**
   * _add_frontend_assets
   *
   * - Fired when styles and scripts are enqueued on the frontend
   * - Enqueue the script that kicks off the orderableness.
   *
   * @access public
   */
  public function _add_frontend_assets() {
    if (tu()->question->type === 'orderable') {
      wp_enqueue_script('tu_frontend_orderable_questions');
      wp_enqueue_style('tu_frontend_orderable_questions');
    }
  }

  /**
   * _render_answers
   *
   * - Fired when the answer inputs for orderable questions should be rendered.
   * - Output a list of the orderable items.
   *   (initially, show the orderable answers in a random order, but after a
   *   trainee has attempted to order them, always show their last order).
   *
   * @param mixed $content
   *
   * @access public
   *
   * @return string The altered content
   */
  public function _render_answers($content, $users_answer, $question) {
    if ($users_answer) {
      $orderable_answers = $users_answer;
    } else {
      $orderable_answers = $question->get_answers();
      shuffle($orderable_answers);
    }

    $view = "{$this->path}/view/answers";
    $data = compact('orderable_answers');

    return new View($view, $data);
  }

  /**
   * _validate_answer
   *
   * - Fired when an orderable question is validated
   * - Return whether the user's attempt at answering the question was
   *   successful or not.
   *
   * @param mixed $correct Whether or not the answer is correct
   * @param mixed $users_answer The user's attempted answer
   * @param mixed $question The question this answer is for
   *
   * @access public
   *
   * @return boolean Whether or not the user answered correctly.
   */
  public function _validate_answer($correct, $users_answer, $question) {
    return (
      array_values((array)$users_answer) === array_values($question->get_answers())
    );
  }

}


add_action('plugins_loaded', function() {
  new Orderable_questions_addon;
});