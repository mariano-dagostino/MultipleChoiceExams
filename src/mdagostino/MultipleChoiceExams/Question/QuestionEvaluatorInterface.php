<?php

namespace mdagostino\MultipleChoiceExams\Question;

interface QuestionEvaluatorInterface {

  public function isCorrect(QuestionInterface $question);

}
