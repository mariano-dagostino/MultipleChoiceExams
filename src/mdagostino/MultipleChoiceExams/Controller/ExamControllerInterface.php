<?php

namespace mdagostino\MultipleChoiceExams\Controller;

use mdagostino\MultipleChoiceExams\Exam\ExamInterface;
use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;

interface ExamControllerInterface {

  public function __construct(ExamInterface $exam, ApprovalCriteriaInterface $approval_criteria);

  public function getApprovalCriteria();

  public function startExam();

  public function finalizeExam();

  public function answerCurrentQuestion(array $answer);

  public function answerQuestion($id, array $answer);

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
