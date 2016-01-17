<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

class BasicApprovalCriteria extends AbstractApprovalCriteria implements ApprovalCriteriaInterface {

  protected $score_to_approve = 60;

  protected $right_sum = 1;

  protected $wrong_rest = 0.3;

  protected $unanswered_rest = 0;

  public function getScoreToApprove() {
    return $this->score_to_approve;
  }

  public function setScoreToApprove($value) {
    $this->score_to_approve = $value;
    return $this;
  }

  public function getRightQuestionsSum() {
    return $this->right_sum;
  }

  public function setRightQuestionsSum($value) {
    $this->right_sum = $value;
    return $this;
  }

  public function getWrongQuestionsRest() {
    return $this->wrong_rest;
  }

  public function setWrongQuestionsRest($value) {
    $this->wrong_rest = $value;
    return $this;
  }

  public function getUnansweredQuestionsRest() {
    return $this->unanswered_rest;
  }

  public function setUnansweredQuestionsRest($value) {
    $this->unanswered_rest = $value;
    return $this;
  }

  public function calculateScore(array $questions) {
    $correct = 0;
    $incorrect = 0;
    foreach ($this->answeredQuestions($questions) as $question) {
      $question->isCorrect() ? $correct++ : $incorrect++;
    }
    $question_count = count($questions);
    $not_answered = $question_count - ($correct + $incorrect);

    $percent = 0;
    $percent += $this->getRightQuestionsSum() * $correct;
    $percent -= $this->getWrongQuestionsRest() * $incorrect;
    $percent -= $this->getUnansweredQuestionsRest() * $not_answered;

    return $percent / $question_count * 100.0;
  }

  protected function answeredQuestions(array $questions) {
    return array_filter($questions, function($question) {
      return $question->wasAnswered();
    });
  }

  public function decideIfPass($score) {
    return $score >= $this->getScoreToApprove();
  }
}
