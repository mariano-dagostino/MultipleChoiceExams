<?php

namespace mdagostino\MultipleChoiceExams\Exam;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;
use mdagostino\MultipleChoiceExams\Exception\InvalidQuestionException;

class Exam implements ExamInterface {

  // The questions of the exam. An array of QuestionInterface.
  protected $questions = array();

  // A Boolean value that indicates if the user passed the exam after finish it.
  protected $pass = NULL;

  /**
   * Answer a specific question of an exam.
   *
   * @param  int $question_id
   *   The ID of the question to aswer.
   * @param  array $answer
   *   An array of question options keys that represent the answer of the user.
   */
  public function answerQuestion($question_id, array $answer) {
    $this->getQuestion($question_id)->answer($answer);
    return $this;
  }

  public function questionsAnswered() {
    return array_filter($this->getQuestions(), function($question) {
      return $question->wasAnswered();
    });
  }

  /**
   * Returns the number of questions the user alredy answered.
   *
   * @return int
   */
  public function questionsAnsweredCount() {
    return count($this->questionsAnswered());
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
   * @param  $questions an array of QuestionInterface
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
    return count($this->getQuestions());
  }


  public function resetAnswers() {
    foreach ($this->getQuestions() as $question) {
      $question->resetAnswer();
    }
  }
}
