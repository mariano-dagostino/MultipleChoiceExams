<?php

namespace mdagostino\MultipleChoiceExams\Exam;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\ApprovalCriteriaInterface;

interface ExamInterface {

  public function answerQuestion($question_id, array $answer);

  public function questionsAnswered();

  public function questionsAnsweredCount();

  public function getQuestions();

  public function getQuestion($id);

  public function setQuestions($questions);

  public function getQuestionCount();

}
