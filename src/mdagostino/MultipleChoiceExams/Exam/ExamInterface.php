<?php

namespace mdagostino\MultipleChoiceExams\Exam;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;

interface ExamInterface {

  public function __construct(ApprovalCriteriaInterface $criteria);

  public function setApprovalCriteria(ApprovalCriteriaInterface $criteria);

  public function getApprovalCriteria();

  public function answerQuestion($question_id, $answer);

  public function isApproved();

  public function questionsAnswered();

  public function getQuestions();

  public function getQuestion($id);

  public function setQuestions($questions);

  public function getQuestionCount();

}
