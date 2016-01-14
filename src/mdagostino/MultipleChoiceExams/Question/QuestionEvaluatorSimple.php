<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionEvaluatorSimple implements QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question) {
    return $question->hitCount() == $question->rightChoicesCount() &&
      $question->missCount() == 0;
  }

}
