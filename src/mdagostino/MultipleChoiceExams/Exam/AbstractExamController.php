<?php

namespace mdagostino\MultipleChoiceExams\Exam;

abstract class AbstractExamController implements ExamControllerInterface {

  protected $exam;

  protected $total_questions;

  protected $counter = 0;

  protected $finalized = FALSE;

  public function __construct(ExamInterface $exam) {
    $this->exam = $exam;
    return $this;
  }

  public function startExam() {
    $this->counter = 1;
    $this->total_questions = $this->getExam()->getQuestionCount();
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

  public function tagCurrentQuestion($tag) {
    $this->getCurrentQuestion()->getInfo()->tag($tag);
  }

  public function untagCurrentQuestion($tag) {
    $this->getCurrentQuestion()->getInfo()->untag($tag);
  }

  public function getQuestionsTagged($tag) {
    $questions_tagged = array();
    foreach ($this->getExam()->getQuestions() as $question) {
      if ($question->getInfo()->hasTag($tag)) {
        $questions_tagged[] = $question;
      }
    }
    return $questions_tagged;
  }

  public function getExam() {
    return $this->exam;
  }

  abstract public function answerCurrentQuestion(array $answer);

  public function getCurrentQuestion() {
    return $this->getExam()->getQuestion($this->getCurrentQuestionIndex() - 1);
  }

  public function getQuestionCount() {
    return $this->getExam()->getQuestionCount();
  }

  public function getCurrentQuestionIndex() {
    return $this->counter;
  }

  public function isFirstQuestion() {
    return $this->counter == 1;
  }

  public function isLastQuestion() {
    return $this->counter == $this->getQuestionCount();
  }
}
