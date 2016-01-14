<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

abstract class AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  protected $score = 0;

  public function pass(array $questions) {
    $this->score = $this->calculateScore($questions);

    return $this->decideIfPass($this->score);
  }

  public function getScore() {
    if ($this->score < 0) {
      return 0;
    }

    return $this->score;
  }

  abstract protected function calculateScore(array $questions);

  abstract protected function decideIfPass($score);

}
