<?php

namespace mdagostino\MultipleChoiceExams\Timer;

interface ExamTimerInterface {

  public function remainingTime();

  public function start();

  public function setDuration($duration);

  public function stillHasTime();

}
