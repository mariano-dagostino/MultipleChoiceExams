<?php

namespace mdagostino\MultipleChoiceExams;

interface ExamInterface {

	public function __construct(ApprovalCriteriaInterface $criteria = NULL) ;  

	public function setApprovalCriteria(ApprovalCriteriaInterface $criteria);

	public function getApprovalCriteria($criteria);
	  
	public function start();

	public function reStart();

	public function remainingTime();

	public function answerQuestion($question_id, $answer);

	public function reviewQuestionLater($question_id, $review_later);

	public function hasQuestionsToReview();

	public function finalize();

	public function isApproved(); 

	public function questionsAnswered(); 

	public function getQuestions(); 

	public function getQuestion($id);

	public function setQuestions($questions); 

	public function totalQuestions();

	public function setDuration($duration);

}