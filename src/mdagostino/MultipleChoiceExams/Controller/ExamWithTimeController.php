<?php

namespace mdagostino\MultipleChoiceExams\Controller;

use mdagostino\MultipleChoiceExams\Exception\ExpiredTimeException;
use mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface;

class ExamWithTimeController extends AbstractExamController implements ExamControllerInterface, ExamWithTimeInterface {

  protected $timer = NULL;

  public function getTimer() {
    if (!isset($this->timer)) {
      throw new \Exception("You must define a timer for ExamWithTimeController controllers");
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
    return $this;
  }

  public function answerCurrentQuestion(array $answer) {
    $this->answerQuestion($this->getCurrentQuestionIndex() - 1, $answer);
    return $this;
  }

  public function answerQuestion($id, array $answer) {
    if ($this->getTimer()->stillHasTime() == FALSE) {
      $this->finalizeExam();
      throw new ExpiredTimeException("There is no left time to complete the exam.");
    }

    $this->getExam()->answerQuestion($id, $answer);
    return $this;
  }

}
