<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

interface ApprovalCriteriaInterface {

  public function rulesDescription();

  public function pass(array $questions);

  public function getSettings($key = NULL);

  public function setSettings(array $settings);

}
