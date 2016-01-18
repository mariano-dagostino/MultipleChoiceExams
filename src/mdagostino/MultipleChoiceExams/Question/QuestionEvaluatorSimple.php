<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionEvaluatorSimple implements QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question) {
    // A question is considered correct if the user selected the right choices
    $right_choices_count = count($question->getRightChoices());
    if ($this->questionHitCount($question) != $right_choices_count) {
      return FALSE;
    }

    // and didn't select any wrong choice.
    return $this->questionMissCount($question) == 0;
  }

  protected function questionHitCount($question) {
    $answer = $question->getAnswers();
    $right_choices = $question->getRightChoices();
    return count(array_intersect($answer, $right_choices));
  }

  protected function questionMissCount($question) {
    $answer = $question->getAnswers();
    $choices = $question->getChoices();
    $right_choices = $question->getRightChoices();
    $wrong_choices = array_diff($choices, $right_choices);
    return count(array_intersect($answer, $wrong_choices));
  }

}
