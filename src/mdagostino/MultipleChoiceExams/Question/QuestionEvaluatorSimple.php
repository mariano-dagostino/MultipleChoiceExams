<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionEvaluatorSimple implements QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question) {
    // A question is considered correct if the user selected the right choices
    // and didn't select any wrong choice.
    return $this->questionHitCount($question) == count($question->getRightAnswers()) &&
      $this->questionMissCount($question) == 0;
  }

  protected function questionHitCount($question) {
    $correct_choices = 0;
    foreach ($question->getChossenAnswers() as $key) {
      if (in_array($key, $question->getRightAnswers())) {
        $correct_choices++;
      }
    }
    return $correct_choices;
  }

  protected function questionMissCount($question) {
    $incorrect_choices = 0;
    foreach ($question->getChossenAnswers() as $key) {
      if (!in_array($key, $question->getRightAnswers())) {
        $incorrect_choices++;
      }
    }
    return $incorrect_choices;
  }

}
