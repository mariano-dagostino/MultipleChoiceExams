<?php

namespace mdagostino\MultipleChoiceExams\Controller;

use mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface;

interface ExamWithTimeInterface {

  public function getTimer();

  public function setTimer(ExamTimerInterface $timer);

}
