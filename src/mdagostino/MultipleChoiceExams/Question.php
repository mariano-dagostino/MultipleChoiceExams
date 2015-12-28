<?php

namespace mdagostino\MultipleChoiceExams;

class Question implements QuestionInterface{

  // A short title to resume the Question.
  protected $title;

  // The question body
  protected $body;

  // A topic for this questions. Approval criterias can use this value to group
  // questions and define if a minimun percentage was reached for each topic.
  protected $topic;

  // An id to match to other data of this question,
  // for example the ID of the question in a database table.
  protected $internal_id;

  // A boolean value to indicate if this question is correct or incorrect.
  protected $correct = FALSE;

  // A keyed pair of key => text options where text is represent a possible
  // answer to this question.
  protected $available_answers = array();

  // An array of keys to identify the right answers of this question.
  protected $right_answers = array();

  // The selected options by the user to this question.
  protected $selected_answers = array();

  // If TRUE this question will be marked to be reviewed later.
  protected $review_later = FALSE;

  /**
   * Return TRUE if the user already defined an answer for this question.
   *
   * @return boolean
   */
  public function wasAnswered() {
    return !empty($this->selected_answers);
  }

  /**
   * Return in wich percentaje this question was correct. This can be used by
   * some Approval Criterias to create less rigid approval criterias.
   *
   * @return int A number between 0 and 100.
   */
  public function correctPercent() {
    $correct_choices = 0;
    $incorrect_choices = 0;
    foreach ($this->selected_answers as $key) {
      if (in_array($key, $this->right_answers)) {
        $correct_choices++;
      }
      else {
        $incorrect_choices++;
      }
    }
    $percent = intval(round($correct_choices - $incorrect_choices) * 100 / count($this->right_answers));
    if ($percent > 0) {
      return $percent;
    }
    return 0;
  }

  /**
   * Returns if this question was marked to be reviewed later.
   *
   * @return boolean
   */
  public function isMarkedToReviewLater() {
    return $this->review_later;
  }


  /**
   * Mark or unmark this question to be reviewed later.
   *
   * @param boolean $review_later
   *   If TRUE this question will be marked to be reviewed later.
   */
  public function reviewLater($review_later) {
    $this->review_later = $review_later;
    return TRUE;
  }

  /**
   * Answer a this question using the answers defined by the user.
   *
   * @param  array $keys
   *   An array of ids that represent the options chossed by the user
   */
  public function answer($keys) {
    $valid_keys = $this->validKeys();
    foreach ($keys as $key) {
      if (!in_array($key, $valid_keys)) {
        throw new InvalidAnswerException("The key $key is not a valid answer for the question {$this->getTitle()}");
      }
    }

    $this->selected_answers = $keys;
    $this->checkAnswers();
    return $this;
  }

  /**
   * Reset the status of this question. This questions was never answered.
   */
  public function forgotAnswer() {
    $this->selected_answers = array();
    $this->correct = FALSE;
    $this->review_later = FALSE;
  }

  /**
   * Validates if all the answers choosen for this question were correct.
   */
  protected function checkAnswers() {
    // Check that the number of answers provided by the user are the same than
    // the right answers.
    if (count($this->selected_answers) != count($this->right_answers)) {
      $this->correct = FALSE;
      return $this;
    }

    // Check all the options were correct.
    foreach ($this->selected_answers as $answer) {
      if (!in_array($answer, $this->right_answers)) {
        $this->correct = FALSE;
        return $this;
      }
    }

    $this->correct = TRUE;
    return $this;
  }

  /**
   * Returns an array of ids that are valid options to answer this question.
   *
   * @return array
   */
  public function validKeys() {
    return array_keys($this->available_answers);
  }

  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setInternalId($internal_id) {
    $this->internal_id = $internal_id;
    return $this;
  }

  public function getInternalId() {
    return $this->internal_id;
  }

  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  public function getDescription() {
    return $this->description;
  }

  public function setTopic($topic) {
    $this->topic = $topic;
    return $this;
  }

  public function getTopic() {
      return $this->topic;
  }

  public function setAvailableAnswers($answers) {
    $this->available_answers = $answers;
    return $this;
  }

  public function getAvailableAnswers() {
    return $this->available_answers;
  }

  public function setRightAnswers($answers) {
    $this->right_answers = $answers;
    return $this;
  }

  public function getRightAnswers() {
    return $this->right_answers;
  }

}
