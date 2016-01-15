<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

abstract class AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  protected $score = 0;

  protected $approved = NULL;

  /**
   * Return TRUE if the user pass this exam. FALSE otherwise.
   *
   * @return boolean
   */
  public function isApproved(array $questions) {
    if (!isset($this->approved)) {
      $this->score = $this->calculateScore($questions);
      $this->approved = $this->decideIfPass($this->score);
    }

    return $this->approved;
  }

  public function reset() {
    $this->score = 0;
    $this->approved = NULL;
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
