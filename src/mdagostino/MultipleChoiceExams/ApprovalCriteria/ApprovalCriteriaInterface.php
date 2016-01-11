<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

interface ApprovalCriteriaInterface {

  public function rulesDescription();

  public function pass(array $questions);

  public function getSettings();

  public function setSettings($settings);

}
