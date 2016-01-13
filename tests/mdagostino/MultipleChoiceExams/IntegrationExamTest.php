<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Exam\Exam;
use mdagostino\MultipleChoiceExams\Exam\ExamWithTimeController;
use mdagostino\MultipleChoiceExams\Question\Question;
use mdagostino\MultipleChoiceExams\Timer\ExamTimer;
use mdagostino\MultipleChoiceExams\Question\QuestionInfo;
use mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorSimple;
use mdagostino\MultipleChoiceExams\ApprovalCriteria\BasicApprovalCriteria;

class IntegrationExamTest extends \PHPUnit_Framework_TestCase {

  public function testExamBasicWorkflow() {

    $approval_criteria = new BasicApprovalCriteria();
    $approval_criteria->setSettings(array(
      'percent_to_approve_exam' => 60,
      'right_questions_sum' => 1.0,
      'unanswered_questions_sum' => 0,
      'wrong_questions_sum' => 0,
    ));
    $exam = new Exam($approval_criteria);

    $examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    // The time is in seconds, 30 minutes are 1800 seconds.
    $examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->andReturn(TRUE)
    ->shouldReceive('getTime')->andReturn(1);


    $question_evaluator = new QuestionEvaluatorSimple();
    for ($i=0; $i < 100; $i++) {

      $available_answers = array(
       'one' => 'One',
       'two' => 'Two',
       'three' => 'Three'
      );
      $right_answers = array('one', 'three');

      $question_info = new QuestionInfo();
      $question = new Question($question_evaluator, $question_info);
      $question
        ->setAnswers($available_answers, $right_answers)
        ->getInfo()
          ->setTitle('Question ' . $i)
          ->setDescription('Description for question ' . $i);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $controller = new ExamWithTimeController($exam);
    $controller->setTimer($examTimer);
    $controller->startExam();

    $controller->answerCurrentQuestion(array('one'));
    $controller->moveToNextQuestion();
    $controller->answerCurrentQuestion(array('one', 'two'));
    $controller->moveToNextQuestion();
    $controller->answerCurrentQuestion(array('one', 'three'));
    $controller->moveToNextQuestion();

    $controller->finalizeExam();

    $this->assertFalse($controller->getExam()->isApproved());
  }

  public function testExamApproved() {
    $approval_criteria = new BasicApprovalCriteria();
    $approval_criteria->setSettings(array(
      'percent_to_approve_exam' => 60,
      'right_questions_sum' => 1.0,
      'unanswered_questions_sum' => 0,
      'wrong_questions_sum' => 0,
    ));
    $exam = new Exam($approval_criteria);

    $examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    $examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->andReturn(TRUE)
    ->shouldReceive('getTime')->andReturn(1);

    $question_evaluator = new QuestionEvaluatorSimple();
    for ($i=0; $i < 100; $i++) {

      $available_answers = array(
       'one' => 'One',
       'two' => 'Two',
       'three' => 'Three'
      );
      $right_answers = array('one', 'three');

      $question_info = new QuestionInfo();
      $question = new Question($question_evaluator, $question_info);
      $question
        ->setAnswers($available_answers, $right_answers)
        ->getInfo()
          ->setTitle('Question ' . $i)
          ->setDescription('Description for question ' . $i);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $controller = new ExamWithTimeController($exam);
    $controller->setTimer($examTimer);
    $controller->startExam();


    // Answer correctly 70% of the answers
    for ($i = 0; $i < 70; $i++) {
      $this->assertFalse($controller->getCurrentQuestion()->wasAnswered());
      $controller->answerCurrentQuestion(array('one', 'three'));
      $this->assertTrue($controller->getCurrentQuestion()->wasAnswered());
      $controller->moveToNextQuestion();
    }

    $controller->finalizeExam();

    $this->assertTrue($controller->getExam()->isApproved());
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\Exception\ExpiredTimeException
   * @expectedExceptionMessage There is no left time to complete the exam.
   */
  public function testNoMoreTime() {
    $approval_criteria = new BasicApprovalCriteria();
    $exam = new Exam($approval_criteria);

    $examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    $examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->andReturn(TRUE, TRUE, TRUE, FALSE);

    $question_evaluator = new QuestionEvaluatorSimple();
    for ($i=0; $i < 100; $i++) {

      $available_answers = array(
       'one' => 'One',
       'two' => 'Two',
       'three' => 'Three'
      );
      $right_answers = array('one', 'three');

      $question_info = new QuestionInfo();
      $question = new Question($question_evaluator, $question_info);
      $question
        ->setAnswers($available_answers, $right_answers)
        ->getInfo()
          ->setTitle('Question ' . $i)
          ->setDescription('Description for question ' . $i);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $controller = new ExamWithTimeController($exam);
    $controller->setTimer($examTimer);
    $controller->startExam();


    // Answer correctly 70% of the answers
    for ($i=0; $i < 70; $i++) {
      $controller->answerCurrentQuestion(array('one', 'three'));
      $controller->moveToNextQuestion();
    }

    $controller->finalizeExam();

    $this->assertTrue($controller->getExam()->isApproved());
  }

}

