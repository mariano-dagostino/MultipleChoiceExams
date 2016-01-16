<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionInfo implements QuestionInfoInterface {

  // A short title to resume the Question.
  protected $title;

  // The question body
  protected $description;

  protected $answer_descriptions;

  protected $tags = array();

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

  public function getChoicesDescriptions() {
    return $this->choices_descriptions;
  }

  public function setChoicesDescriptions(array $answer_descriptions) {
    $this->choices_descriptions = $answer_descriptions;
    return $this;
  }

  public function hasTag($tag) {
    return !empty($this->tags[$tag]);
  }

  public function tag($tag) {
    $this->tags[$tag] = TRUE;
    return $this;
  }

  public function untag($tag) {
    if ($this->hasTag($tag)) {
      unset($this->tags[$tag]);
    }
    return $this;
  }
}
