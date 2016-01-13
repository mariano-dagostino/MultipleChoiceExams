<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Exam\ExamWithTimeController;

class ExamControllerTest extends \PHPUnit_Framework_TestCase {

  protected $exam;

  protected $questions = array();

  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {
    $this->questions = array();
    for ($i = 0; $i < 10; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      $this->questions[] = $question;
    }

    $this->examTimer = \Mockery::mock('mdagostino\MultipleChoiceExams\Timer\ExamTimerInterface');

    $this->examTimer
    ->shouldReceive('start')->once()
    ->shouldReceive('stillHasTime')->andReturn(TRUE);

    $this->exam = \Mockery::mock('mdagostino\MultipleChoiceExams\Exam\ExamInterface');
    $this->exam
    ->shouldReceive('getQuestion')
    ->andReturnUsing(function($argument) {
        return $this->questions[$argument];
    })
    ->shouldReceive('isApproved')->andReturn(TRUE)
    ->shouldReceive('getCurrentQuestion')->andReturn(\Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface'))
    ->shouldReceive('totalQuestions')->andReturn(count($this->questions));
  }

  public function testExamControlerCreation() {
    $controller = new ExamWithTimeController($this->exam);
    $controller->setTimer($this->examTimer);
    $controller->startExam();
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");
    $this->assertEquals($controller->getQuestionCount(), 10, "The are 10 questions in the current exam");
    $this->assertEquals($controller->getExam(), $this->exam);
  }


  public function testExamControllerQuestionNavigation() {
    $controller = new ExamWithTimeController($this->exam);
    $controller->setTimer($this->examTimer);
    $controller->startExam();
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");

    $controller->moveToNextQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 2, "The second question is numbered with 1");

    $controller->moveToPreviousQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");

    $controller->moveToLastQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 10, "The last question is numbered with 10");

    $controller->moveToFirstQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");

    // Try out of range movements
    $controller->moveToLastQuestion();
    $controller->moveToNextQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 10, "The last question is numbered with 10");

    // Try negative movement
    $controller->moveToFirstQuestion();
    $controller->moveToPreviousQuestion();
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");
  }

  public function testGetCurrentQuestion() {
    $controller = new ExamWithTimeController($this->exam);
    $controller->setTimer($this->examTimer);
    $controller->startExam();
    $this->assertEquals($controller->getCurrentQuestion(), $this->questions[0]);

    $controller->moveToNextQuestion();
    $this->assertEquals($controller->getCurrentQuestion(), $this->questions[1]);

    $controller->moveToLastQuestion();
    $this->assertEquals($controller->getCurrentQuestion(), end($this->questions));
  }

  public function testReviewQuestionsLater() {
    $controller = new ExamWithTimeController($this->exam);
    $controller->setTimer($this->examTimer);

    $questions = array();
    for ($i = 1; $i <= 5 ; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      $questions[$i] = $question;
    }

    $first_question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
    $first_question->shouldReceive('reviewLater')->with(TRUE);

    $second_question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
    $second_question->shouldReceive('reviewLater')->with(FALSE);
    $this->questions = array($first_question, $second_question);

    $controller->startExam();

    // Mark the first question to be reviewed later
    $controller->markCurrentQuestionForLaterReview();

    // Mark the second question to not be reviewd later
    $controller->moveToNextQuestion();
    $controller->unmarkCurrentQuestionForLaterReview();
  }

  public function testFinalizeExam() {
    $controller = new ExamWithTimeController($this->exam);
    $controller->setTimer($this->examTimer);

    $controller->startExam();
    $controller->finalizeExam();
  }


}

