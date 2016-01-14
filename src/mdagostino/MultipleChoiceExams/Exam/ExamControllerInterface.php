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

  public function tagCurrentQuestion($tag);

  public function untagCurrentQuestion($tag);

  public function getQuestionsTagged($tag);

  public function getExam();

  public function getCurrentQuestion();

  public function getQuestionCount();

  public function getCurrentQuestionIndex();

}
