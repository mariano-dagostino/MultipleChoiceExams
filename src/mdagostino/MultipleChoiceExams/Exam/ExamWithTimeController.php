<?php

namespace mdagostino\MultipleChoiceExams\Exam;

use mdagostino\MultipleChoiceExams\Exception\ExpiredTimeException;
use mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface;

class ExamWithTimeController extends AbstractExamController implements ExamControllerInterface {

  protected $timer = NULL;

  public function getTimer() {
    if (!isset($this->timer)) {
      throw new Exception("You must define a timer for ExamWithTimeController controllers");
    }

    return $this->timer;
  }

  public function setTimer(ExamTimerInterface $timer) {
    $this->timer = $timer;
    return $this;
  }

  public function startExam() {
    parent::startExam();
    $this->getTimer()->start();
  }

  public function answerCurrentQuestion(array $answer) {
    if ($this->getTimer()->stillHasTime() == FALSE) {
      $this->finalizeExam();
      throw new ExpiredTimeException("There is no left time to complete the exam.");
    }

    $this->getExam()->answerQuestion($this->getCurrentQuestionIndex() - 1, $answer);
  }

}
