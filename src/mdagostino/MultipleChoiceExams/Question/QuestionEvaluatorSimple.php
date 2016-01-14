<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionEvaluatorSimple implements QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question) {
    return $question->hitCount() == count($question->getRightAnswers()) &&
      $question->missCount() == 0;
  }

}
