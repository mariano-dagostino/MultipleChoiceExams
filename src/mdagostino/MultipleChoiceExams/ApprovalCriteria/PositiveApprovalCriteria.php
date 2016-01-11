<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

class PositiveApprovalCriteria implements ApprovalCriteriaInterface {

  // The questions of the exam. An array of MultipleChoiceExams\Question.
  protected $questions;

  // The settings of this approval criteria.
  protected $settings;

  public function __construct() {
    $this->settings = array(
      // By default require 60% of right answered questions to pass this exam
      'approval_percent_exam' => 60,
      //A question is approved when the 80% of the selected options are right.
      'approval_percent_question' => 80,
    );
  }

  public function rulesDescription() {
    $rules = array(
      'The exam is approved by answering correctly a minimum number of questions.',
      'Only right answered questions are considered.',
      'No penalty for wrong answered questions.',
      'Unanswered questions are not considered either.'
    );
    return $rules;
  }
  // Returns true if the question is approved, else false.

  protected function isCorrect($question){
    if ($question->wasAnswered()) {
        if ($question->correctPercent() >= $this->settings['approval_percent_question']) {
          return TRUE;
        }
        else {
          return FALSE;
        }
  }
}

  public function pass(array $questions) {
    $this->questions = $questions;

    $questions_correctly_answered = 0;
    foreach ($this->questions as $question) {
      if ($question->isCorrect($question)) {
          $questions_correctly_answered++;
        }
      }

    return $questions_correctly_answered >= $this->questionsRequiredToPass();
  }

  public function setSettings($settings = array()) {
    $this->settings = array_merge($this->settings, $settings);
    return $this;
  }

  public function getSettings() {
    return $this->settings;
  }

  public function setQuestions(array $questions) {
    $this->questions = $questions;
    return $this;
  }

  public function questionsRequiredToPass() {
    if (empty($this->questions)) {
      throw new \Exception("There are not defined questions to analyse");
    }

    if (empty($this->settings['approval_percent_exam'])) {
      throw new Exception("There is no approval percent defined");
    }

    return intval(count($this->questions) * $this->settings['approval_percent_exam'] / 100);
  }

}
