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

  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {

    $this->approval_criteria = new BasicApprovalCriteria();
    $this->approval_criteria
      ->setScoreToApprove(60)
      ->setRightQuestionsSum(1)
      ->setWrongQuestionsRest(0);

  }

  public function testExamBasicWorkflow() {
    $exam = new Exam($this->approval_criteria);

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

    $examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    $examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->times(3)->andReturn(TRUE);

    $controller->setTimer($examTimer);
    $controller->startExam();

    $controller->answerCurrentQuestion(array('one'));
    $controller->moveToNextQuestion();
    $controller->answerCurrentQuestion(array('one', 'two'));
    $controller->tagCurrentQuestion('review_later');
    $controller->moveToNextQuestion();
    $controller->answerCurrentQuestion(array('one', 'three'));
    $controller->tagCurrentQuestion('review_later');
    $controller->tagCurrentQuestion('hard_question');
    $controller->moveToNextQuestion();

    $controller->finalizeExam();

    $this->assertFalse($controller->getExam()->isApproved());
    $this->assertEquals($controller->getQuestionsTagged('review_later'), array($questions[1], $questions[2]));
    $this->assertEquals($controller->getQuestionsTagged('hard_question'), array($questions[2]));
    $this->assertEmpty($controller->getQuestionsTagged('not used'));
  }

  public function testExamApproved() {
    $exam = new Exam($this->approval_criteria);

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

    $examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    $examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->times(70)->andReturn(TRUE);

    $controller = new ExamWithTimeController($exam);
    $controller->setTimer($examTimer);
    $controller->startExam();

    // Answer correctly 70% of the answers
    for ($i = 1; $i <= 70; $i++) {
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
    $exam = new Exam($this->approval_criteria);

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
