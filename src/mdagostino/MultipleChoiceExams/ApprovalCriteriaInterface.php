<?php

namespace mdagostino\MultipleChoiceExams;

interface ApprovalCriteriaInterface {

  public function rulesDescription();

  public function pass();

  public function setQuestions($questions);

  public function setSettings($settings);

  public function getSettings();

}
