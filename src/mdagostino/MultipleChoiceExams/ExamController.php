<?php

namespace mdagostino\MultipleChoiceExams;

class ExamController implements ExamControllerInterface {

  protected $exam;

  protected $current_question;

  protected $total_questions;

  protected $counter = 0;

  public function startExam($exam) {
    $this->exam = $exam;
    $this->counter = 0;
    $this->current_question = $this->getCurrentQuestion();
    $this->total_questions = $this->exam->totalQuestions();
    $this->exam->start();
  }

  public function finalizeExam() {
    $this->exam->finalize();
    return $this;
  }

  public function moveToFirstQuestion() {
    $this->counter = 0;
    $this->current_question = $this->exam->getQuestion($this->counter);
    return $this;
  }

  public function moveToNextQuestion() {
    if ($this->counter < $this->total_questions - 1) {
      $this->counter++;
      $this->current_question = $this->exam->getQuestion($this->counter);
    }
    return $this;
  }

  public function moveToPreviousQuestion() {
    if ($this->counter > 0) {
      $this->counter--;
      $this->current_question = $this->exam->getQuestion($this->counter);
    }
    return $this;
  }

  public function moveToLastQuestion() {
    $this->counter = $this->total_questions - 1;
    $this->current_question = $this->exam->getQuestion($this->counter);
    return $this;
  }

  public function markCurrentQuestionForLaterReview() {
    $this->exam->markToReviewLater($this->counter, TRUE);
  }

  public function unmarkCurrentQuestionForLaterReview() {
    $this->exam->markToReviewLater($this->counter, FALSE);
  }

  public function getCurrentExam() {
    return $this->exam;
  }

  public function getCurrentQuestion() {
    return $this->exam->getQuestion($this->counter);
  }

  public function getQuestionCount() {
    return $this->exam->totalQuestions();
  }

  public function getCurrentQuestionCount() {
    return $this->counter + 1;
  }

}
