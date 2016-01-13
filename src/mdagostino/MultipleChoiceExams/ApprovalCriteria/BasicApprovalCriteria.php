<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

class BasicApprovalCriteria extends AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  public function __construct() {
    $this->settings = array(
      'percent_to_approve_exam' => 60,
      'right_questions_sum' => 1.0,
      'unanswered_questions_sum' => 0,
      'wrong_questions_sum' => -0.3,
    );
  }

  public function rulesDescription() {
    $right_sum = $this->getSettings('right_questions_sum');
    $not_answered_sum = $this->getSettings('unanswered_questions_sum');
    $wrong_sum = $this->getSettings('wrong_questions_sum');
    $percent = $this->getSettings('percent_to_approve_exam');

    $rules = array();
    $rules[] = "The exam is approved with $percent%";

    if ($not_answered_sum != 0 || $wrong_sum != 0) {
      $rules[] = 'Correclty answered questions are considered ' . sprintf("%+.2f", $right_sum);
      $rules[] = 'Wrong answered questions are considered ' . sprintf("%+.2f", $wrong_sum);
      $rules[] = 'Unanswered questions are considered ' . sprintf("%+.2f", $not_answered_sum);
    }

    return $rules;
  }

  public function calculateScore(array $questions) {
    $correct = 0;
    $incorrect = 0;
    foreach ($questions as $question) {
      if ($question->wasAnswered()) {
        $question->isCorrect() ? $correct++ : $incorrect++;
      }
    }
    $question_count = count($questions);
    $not_answered = $question_count - ($correct + $incorrect);

    $right_sum = $this->getSettings('right_questions_sum');
    $not_answered_sum = $this->getSettings('unanswered_questions_sum');
    $wrong_sum = $this->getSettings('wrong_questions_sum');

    $percent = ($correct * $right_sum + $not_answered_sum * $not_answered + $incorrect * $wrong_sum) / $question_count;

    return $percent * 100.0;
  }

  public function decideIfPass($score) {
    $percent_to_approve = $this->getSettings('percent_to_approve_exam');
    return $score >= $percent_to_approve;
  }
}
