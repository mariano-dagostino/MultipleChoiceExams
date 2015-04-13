<?php

namespace mdagostino\MultipleChoiceExams;

class PositiveNegativeApprovalCriteria extends PositiveApprovalCriteria implements ApprovalCriteriaInterface {

  public function rulesDescription() {
    $rules = array(
      'The exam is aproved by anwsering correctly a minimun number of questions.',
      'Only right answered questions are considered.',
      'Wrong answered questions are considered minus one.',
      'Unanswered questions are not considered either.'
    );
    return $rules;
  }

  public function pass() {
    $questions_correctly_answered = 0;
    foreach ($this->questions as $question) {
      if ($question->wasAnswered()) {
        if ($question->isCorrect()) {
          $questions_correctly_answered++;
        }
        else {
          $questions_correctly_answered--;
        }
      }
    }

    if ($questions_correctly_answered >= $this->questionsRequiredToPass()) {
      return TRUE;
    }
    return FALSE;
  }
}
