<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

abstract class AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  protected $settings;

  protected $score;

  public function __construct() {
    $this->settings = array(
      'percent_to_approve_exam' => 60,
    );
  }

  abstract function rulesDescription();

  abstract protected function calculateScore(array $questions);

  abstract protected function decideIfPass($score);

  public function pass(array $questions) {
    $this->score = $this->calculateScore($questions);

    return $this->decideIfPass($this->score);
  }

  public function setSettings(array $settings) {
    $this->settings = array_merge($this->settings, $settings);
    return $this;
  }

  public function getSettings($key = NULL) {
    if (!empty($key) && !isset($this->settings[$key])) {
      return NULL;
    }

    if (isset($this->settings[$key])) {
      return $this->settings[$key];
    }

    return $this->settings;
  }

  public function getScore() {
    return $this->score;
  }

}
