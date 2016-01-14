<?php

namespace mdagostino\MultipleChoiceExams\Exam;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;
use mdagostino\MultipleChoiceExams\Exception\InvalidQuestionException;

class Exam implements ExamInterface {

  // The questions of the exam. An array of MultipleChoiceExams\QuestionInterface.
  protected $questions = array();

  // A Boolean value that indicates if the user passed the exam after finish it.
  protected $pass = NULL;

  protected $approval_criteria = NULL;


  public function __construct(ApprovalCriteriaInterface $criteria) {
    $this->approval_criteria = $criteria;
  }

  /**
   * Set the Approval Criteria.
   *
   * @param ApprovalCriteriaInterface $criteria
   */
  public function setApprovalCriteria(ApprovalCriteriaInterface $criteria) {
    $this->approval_criteria = $criteria;
    return $this;
  }

  public function getApprovalCriteria() {
    return $this->approval_criteria;
  }


  /**
   * Answer a specific question of an exam.
   *
   * @param  int $question_id
   *   The ID of the question to aswer.
   * @param  array $answer
   *   An array of question options keys that represent the answer of the user.
   */
  public function answerQuestion($question_id, $answer) {
    if (!isset($this->questions[$question_id])) {
      throw new InvalidQuestionException("There is no questions with the id $question_id");
    }

    if (!is_array($answer)) {
      $answer = array($answer);
    }

    $this->questions[$question_id]->answer($answer);
    return $this;
  }

  /**
   * Return TRUE if the user pass this exam. FALSE otherwise.
   *
   * @return boolean
   */
  public function isApproved() {

    if (!isset($this->pass)) {
      $this->pass = $this->approval_criteria->pass($this->getQuestions());
    }

    return $this->pass;
  }

  /**
   * Returns the number of questions the user alredy answered.
   *
   * @return int
   */
  public function questionsAnswered() {
    $count = 0;
    foreach ($this->questions as $question) {
      if ($question->wasAnswered()) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Return the list of questions of this exam.
   *
   * @return array
   */
  public function getQuestions() {
    return $this->questions;
  }

  /**
   * Return the list of questions of this exam.
   *
   * @return array
   * @throws InvalidQuestionException
   */
  public function getQuestion($id) {
    if (!isset($this->questions[$id])) {
      throw new InvalidQuestionException("There is no question with id $id");
    }

    return $this->questions[$id];
  }

  /**
   * Defines the questions of this exam.
   * @param  $questions an array of MultipleChoiceExams\Question
   */
  public function setQuestions($questions) {
    $this->questions = $questions;
    return $this;
  }

  /**
   * Returns the total amount of questions of this exam.
   *
   * @return int
   */
  public function getQuestionCount() {
    return count($this->questions);
  }
}
