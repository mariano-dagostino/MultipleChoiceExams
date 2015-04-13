<?php

namespace mdagostino\MultipleChoiceExams;

interface ExamControllerInterface {

  public function startExam($exam);

  public function finalizeExam();

  public function moveToFirstQuestion();

  public function moveToNextQuestion();

  public function moveToPreviousQuestion();

  public function moveToLastQuestion();

  public function markCurrentQuestionForLaterReview();

  public function unmarkCurrentQuestionForLaterReview();

  public function getCurrentExam();

  public function getCurrentQuestion();

  public function getQuestionCount();

  public function getCurrentQuestionCount();

}
