<?php

namespace mdagostino\MultipleChoiceExams\Question;

interface QuestionInfoInterface {

  public function setTitle($title);

  public function getTitle();

  public function setDescription($description);

  public function getDescription();

  public function getChoicesDescriptions();

  public function setChoicesDescriptions(array $answers);

  public function hasTag($tag);

  public function tag($tag);

  public function untag($tag);
}
