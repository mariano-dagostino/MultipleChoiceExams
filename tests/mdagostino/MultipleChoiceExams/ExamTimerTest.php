<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Timer\ExamTimer;

class ExamTimerTest extends \PHPUnit_Framework_TestCase {


  public function testTimer() {
    $timer = new ExamTimer;

    $timer->start();
    $timer->setDuration(10); // Minutes
    $this->assertTrue($timer->stillHasTime());
    $this->assertTrue($timer->remainingTime() > 60 * 9);
  }

}
