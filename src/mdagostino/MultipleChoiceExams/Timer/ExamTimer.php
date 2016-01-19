<?php

namespace mdagostino\MultipleChoiceExams\Timer;

class ExamTimer implements ExamTimerInterface {

  // The time available to complete the exam in minutes.
  protected $duration = 60;

  // Timestamp. The time this exam started.
  protected $started_time = 0;

  /**
   * Return the amount of seconds available to finish this exam.
   */
  public function remainingTime() {

    if ($this->started_time + $this->duration * 60 - $this->getTime() < 0) {
      return 0;
    }

    return $this->started_time + $this->duration * 60 - $this->getTime() ;

  }

  /**
   * Defines the duration of the exam in minutes.
   */
  public function setDuration($duration) {
    $this->duration = $duration;
    return $this;
  }

  public function setStartedAt($time) {
    return $this->started_time = $time;
    return $this;
  }

  public function start() {
    return $this->started_time = $this->getTime();
  }

  public function stillHasTime() {
    return $this->remainingTime() > 0;
  }

  protected function getTime() {
    return time();
  }

}

