<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

class PositiveApprovalCriteria extends AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  public function __construct() {
    $this->settings = array(
      'percent_to_approve_exam' => 60,
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

  public function calculateScore(array $questions) {
    $correct = 0;
    foreach ($questions as $question) {
      if ($question->isCorrect($question)) {
        $correct++;
      }
    }

    return $correct / count($questions) * 100.0;
  }

  public function decideIfPass($score) {
    $to_approve = $this->getSettings('percent_to_approve_exam');
    return $score >= $to_approve;
  }

}
