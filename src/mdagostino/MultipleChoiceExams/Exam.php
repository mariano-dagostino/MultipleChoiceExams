<?php

namespace mdagostino\MultipleChoiceExams;

class Exam implements ExamInterface{

  // The time available to complete the exam.
  protected $length = 60;

  // Boolean, if TRUE the exam has started.
  protected $started = FALSE;

  // Timestamp. The time this exam started.
  protected $started_time = 0;

  // Boolean, if TRUE the exam time has ended or the user submitted his answer.
  protected $finished = FALSE;

  // The questions of the exam. An array of MultipleChoiceExams\Question.
  protected $questions = array();

  // A Boolean value that indicates if the user passed the exam after finish it.
  protected $pass = FALSE;

  protected $approval_criteria = NULL;

  public function __construct(ApprovalCriteriaInterface $criteria = NULL) {
    if (is_null($criteria)) {
      $this->approval_criteria = new PositiveApprovalCriteria();
    }
}  
  /**
   * Set the Approval Criteria.
   *
   * @param ApprovalCriteriaInterface $criteria
   */
  public function setApprovalCriteria(ApprovalCriteriaInterface $criteria) {
    $this->approval_criteria = $criteria;
    return $this;
  }

  public function getApprovalCriteria($criteria) {
    return $this->approval_criteria;
  }

  /**
   * Start the exam.
   */
  public function start() {
    $this->started_time = time();
    $this->started = TRUE;
    $this->finished = FALSE;
    $this->pass = FALSE;
    return $this;
  }

  /**
   * Re-Start the exam.
   */
  public function reStart() {
    $this->start();
    foreach ($this->getQuestions() as $question) {
      $question->resetAnswer();
    }
    return $this;
  }


  /**
   * Return the amount of seconds available to finish this exam.
   */
  public function ramainingTime() {
    return time() - $this->started_time;
  }

  /**
   * Answer a specific question of an exam.
   *
   * @param  int $question_id
   *   The ID of the question to aswer.
   * @param  array $answer
   *   An array of question options keys that represent the answer of the user.
   */
  public function answerQuestion($question_id, $answer) {
    if (!empty($this->questions[$question_id])) {
      $this->questions[$question_id]->answer($answer);
    }
    else {
      throw new ExceptionInvalidQuestion("There is no questions with the id $questionId");
    }
    return $this;
  }


  /**
   * Mark or unmark a specific question of an exam to review later.
   *
   * @param  int $question_id
   *   The ID of the question to aswer.
   * @param  boolean $review_later
   *   If TRUE the specified question will be marked to be reviewed later.
   */
  public function reviewQuestionLater($question_id, $review_later) {
    if (!empty($this->questions[$question_id])) {
      $this->questions[$question_id]->reviewLater($review_later);
    }
    else {
      throw new ExceptionInvalidQuestion("There is no questions with the id $question_id");
    }
    return $this;
  }

  /**
   * Returns TRUE if at least one of the questions of this exam was marked to be
   * reviewed later.
   *
   * @return boolean
   */
  public function hasQuestionsToReview() {
    foreach ($this->questions as $question) {
      if ($question->isMarkedToReviewLater()) {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Finalizes the exam.
   *
   * Calculates if the user pass based of the amount of questions properly
   * answered.
   */
  public function finalize() {
    if ($this->finished) {
      throw new Exception("This exam has already been finished.");
    }

    $this->approval_criteria->setQuestions($this->getQuestions());
    $this->pass = $this->approval_criteria->pass();

    $this->finished = TRUE;
    return $this;
  }

  /**
   * Return TRUE if the user pass this exam. FALSE otherwise.
   *
   * @return boolean
   */
  public function isApproved() {
    return $this->pass;
  }

  /**
   * Returns the number of questions the user alredy answered.
   *
   * @return int
   */
  public function questionsAnswered() {
    $count = 0;
    foreach ($this->questions as $question) {
      if ($question->wasAnswered()) {
        $count++;
      }
    }
    return $count;
  }

  /**
   * Return the list of questions of this exam.
   *
   * @return array
   */
  public function getQuestions() {
    return $this->questions;
  }


  /**
   * Return the list of questions of this exam.
   *
   * @return array
   * @throws InvalidQuestionException
   */
  public function getQuestion($id) {
    if (!empty($this->questions[$id])) {
      return $this->questions[$id];
    }
    throw new InvalidQuestionException("There is no question with id $id");
  }


  /**
   * Defines the questions of this exam.
   * @param  $questions an array of MultipleChoiceExams\Question
   */
  public function setQuestions($questions) {
    $this->questions = $questions;
    return $this;
  }

  /**
   * Returns the total amount of questions of this exam.
   *
   * @return int
   */
  public function totalQuestions() {
    return count($this->questions);
  }
}
