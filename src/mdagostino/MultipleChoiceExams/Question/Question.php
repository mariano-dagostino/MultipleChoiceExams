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
  protected $choices = array();

  // An array of keys to identify the right answers of this question.
  protected $right_choices = array();

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
    return count($this->getAnswers()) > 0;
  }

  /**
   * Answer a this question using the answers defined by the user.
   *
   * @param  array $keys
   *   An array of ids that represent the options chossed by the user
   */
  public function answer(array $keys) {
    $valid_keys = $this->getChoices();
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
    return $this->getQuestionEvaluator()->isCorrect($this);
  }

  public function setChoices(array $answers, array $right_choices) {
    foreach ($right_choices as $key) {
      if (!isset($answers[$key])) {
        $title =  $this->getInfo()->getTitle();
        throw new InvalidAnswerException("The key '$key' is not a valid answer for the question '$title'");
      }
    }

    // Only save the keys on this object to reduce memory usage
    $this->choices = array_keys($answers);
    // Swapping the QuestionInfo interface could be used to retrive the info
    // from another source like a database.
    $this->getInfo()->setChoicesDescriptions($answers);
    $this->right_choices = $right_choices;
    return $this;
  }

  public function getChoices() {
    return $this->choices;
  }

  public function getRightChoices() {
    return $this->right_choices;
  }

  public function getAnswers() {
    return $this->chossen_answers;
  }

  public function getInfo() {
    return $this->info;
  }

  public function getQuestionEvaluator() {
    return $this->question_evaluator;
  }
}
