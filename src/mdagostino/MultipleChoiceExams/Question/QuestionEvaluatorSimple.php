<?php

namespace mdagostino\MultipleChoiceExams\Question;

class QuestionEvaluatorSimple implements QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question) {
    return $question->correctlyChossenCount() == $question->rightChoicesCount() &&
      $question->incorrectlyChossenCount() == 0;
  }

}
