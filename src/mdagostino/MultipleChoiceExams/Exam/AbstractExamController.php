<?php

namespace mdagostino\MultipleChoiceExams\Exam;

abstract class AbstractExamController implements ExamControllerInterface {

  protected $exam;

  protected $total_questions;

  protected $counter = 0;

  protected $finalized = FALSE;

  protected $review_later = array();


  public function __construct(ExamInterface $exam) {
    $this->exam = $exam;
    return $this;
  }

  public function startExam() {
    $this->counter = 1;
    $this->total_questions = $this->getExam()->totalQuestions();
  }

  /**
   * Re-Start the exam.
   */
  public function reStartExam() {
    $this->startExam();
    foreach ($this->getQuestions() as $question) {
      $question->resetAnswer();
    }
    return $this;
  }

  public function finalizeExam() {
    $this->finalized = TRUE;
    $this->getExam()->isApproved();
    return $this;
  }

  public function moveToFirstQuestion() {
    $this->counter = 1;
    return $this;
  }

  public function moveToNextQuestion() {
    if ($this->counter < $this->total_questions) {
      $this->counter++;
    }
    return $this;
  }

  public function moveToPreviousQuestion() {
    if ($this->counter > 1) {
      $this->counter--;
    }
    return $this;
  }

  public function moveToLastQuestion() {
    $this->counter = $this->total_questions;
    return $this;
  }

  public function markCurrentQuestionForLaterReview() {
    $this->review_later[$this->getCurrentQuestionCount()] = TRUE;
  }

  public function unmarkCurrentQuestionForLaterReview() {
    $this->review_later[$this->getCurrentQuestionCount()] = FALSE;
  }

  /**
   * Returns TRUE if at least one of the questions of this exam was marked to be
   * reviewed later.
   *
   * @return boolean
   */
  public function hasQuestionsToReview() {
    return count(array_filter($this->review_later)) > 0;
  }

  public function questionsToReview() {
    $questions_to_review = array();
    foreach (array_keys(array_filter($this->review_later)) as $id) {
      $questions_to_review[] = $this->getExam()->getQuestion($id);
    }
    return $questions_to_review;
  }

  public function getExam() {
    return $this->exam;
  }

  abstract public function answerCurrentQuestion(array $answer);

  public function getCurrentQuestion() {
    return $this->getExam()->getQuestion($this->getCurrentQuestionCount() - 1);
  }

  public function getQuestionCount() {
    return $this->getExam()->totalQuestions();
  }

  public function getCurrentQuestionCount() {
    return $this->counter;
  }

  public function isFirstQuestion() {
    return $this->counter == 1;
  }

  public function isLastQuestion() {
    return $this->counter == $this->getQuestionCount();
  }
}
