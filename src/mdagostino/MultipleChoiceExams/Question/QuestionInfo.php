<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionInfo implements QuestionInfoInterface {

  // A short title to resume the Question.
  protected $title;

  // The question body
  protected $description;

  public function setTitle($title) {
    $this->title = $title;
    return $this;
  }

  public function getTitle() {
    return $this->title;
  }

  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

  public function getDescription() {
    return $this->description;
  }

}
