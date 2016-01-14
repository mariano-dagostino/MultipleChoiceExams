<?php

namespace mdagostino\MultipleChoiceExams\Question;

use mdagostino\MultipleChoiceExams\Exception\InvalidAnswerException;

class Question implements QuestionInterface {

  // An object that will determinate if this question is or is not correct.
  protected $question_evaluator;

  // The information about this question, like title, explanation, etc.
  protected $info;

  // A keyed pair of key => text options where text is represent a possible
  // answer to this question.
  protected $available_answers = array();

  // An array of keys to identify the right answers of this question.
  protected $right_answers = array();

  // The selected options by the user to this question.
  protected $chossen_answers = array();

  public function __construct(QuestionEvaluatorInterface $question_evaluator, QuestionInfoInterface $info = NULL) {
    $this->question_evaluator = $question_evaluator;

    $this->info = $info;
  }

  /**
   * Return TRUE if the user already defined an answer for this question.
   *
   * @return boolean
   */
  public function wasAnswered() {
    return count($this->getChossenAnswers()) > 0;
  }

  /**
   * Answer a this question using the answers defined by the user.
   *
   * @param  array $keys
   *   An array of ids that represent the options chossed by the user
   */
  public function answer(array $keys) {
    $valid_keys = $this->validKeys();
    foreach ($keys as $key) {
      if (!in_array($key, $valid_keys)) {
        $title =  $this->getInfo()->getTitle();
        throw new InvalidAnswerException("The key '$key' is not a valid answer for the question '$title'");
      }
    }

    $this->chossen_answers = $keys;
    return $this;
  }

  /**
   * Reset the status of this question. This questions was never answered.
   */
  public function resetAnswer() {
    $this->chossen_answers = array();
  }

  public function isCorrect() {
    return $this->question_evaluator->isCorrect($this);
  }

  /**
   * Returns an array of ids that are valid options to answer this question.
   *
   * @return array
   */
  protected function validKeys() {
    return array_keys($this->available_answers);
  }

  public function setAnswers(array $answers, array $right_answers) {
    foreach ($right_answers as $key) {
      if (!isset($answers[$key])) {
        $title =  $this->getInfo()->getTitle();
        throw new InvalidAnswerException("The key '$key' is not a valid answer for the question '$title'");
      }
    }

    $this->available_answers = $answers;
    $this->right_answers = $right_answers;
    return $this;
  }

  public function getAvailableAnswers() {
    return $this->available_answers;
  }

  public function getRightAnswers() {
    return $this->right_answers;
  }

  public function getChossenAnswers() {
    return $this->chossen_answers;
  }

  public function getInfo() {
    return $this->info;
  }

  public function getQuestionEvaluator() {
    return $this->question_evaluator;
  }
}
