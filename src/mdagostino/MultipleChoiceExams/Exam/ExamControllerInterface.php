<?php

namespace mdagostino\MultipleChoiceExams\Exam;

interface ExamControllerInterface {

  public function __construct(ExamInterface $exam);

  public function startExam();

  public function finalizeExam();

  public function moveToFirstQuestion();

  public function moveToNextQuestion();

  public function moveToPreviousQuestion();

  public function moveToLastQuestion();

  public function markCurrentQuestionForLaterReview();

  public function unmarkCurrentQuestionForLaterReview();

  public function hasQuestionsToReview();

  public function questionsToReview();

  public function getExam();

  public function getCurrentQuestion();

  public function getQuestionCount();

  public function getCurrentQuestionCount();

}
