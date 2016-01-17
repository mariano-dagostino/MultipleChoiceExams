<?php

namespace mdagostino\MultipleChoiceExams\Controller;

use mdagostino\MultipleChoiceExams\Exam\ExamInterface;
use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;


abstract class AbstractExamController implements ExamControllerInterface {

  protected $exam;

  protected $total_questions;

  protected $counter = 0;

  protected $finalized = FALSE;

  protected $approval_criteria = NULL;

  public function __construct(ExamInterface $exam, ApprovalCriteriaInterface $approval_criteria) {
    $this->exam = $exam;
    $this->approval_criteria = $approval_criteria;
    return $this;
  }

  public function getApprovalCriteria() {
    return $this->approval_criteria;
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
    $this->getApprovalCriteria()->isApproved($this->getExam()->getQuestions());
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
    return array_filter($this->getExam()->getQuestions(), function($question) use ($tag) {
      return $question->getInfo()->hasTag($tag);
    });
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
