<?php

namespace mdagostino\MultipleChoiceExams;

class ExamControllerTest extends \PHPUnit_Framework_TestCase {

  protected $exam;

  protected $questions = array();

  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {
    $this->questions = array();
    for ($i = 0; $i < 10; $i++) {
      $question = \Mockery::mock('Question');
      $question->shouldReceive('reviewLater');
      $this->questions[] = $question;
    }

    $this->exam = \Mockery::mock('Exam');
    $this->exam
    ->shouldReceive('start')->once()
    ->shouldReceive('getQuestion')
    ->andReturnUsing(function($argument) {
        return $this->questions[$argument];
    })
    ->shouldReceive('finish')
    ->shouldReceive('markToReviewLater')
    ->andReturnUsing(function($question_id, $argument) {
        return $this->questions[$question_id]->reviewLater($argument);
    })
    ->shouldReceive('getCurrentQuestion')->andReturn(\Mockery::mock('Question'))
    ->shouldReceive('totalQuestions')->andReturn(count($this->questions));
  }

  public function testExamControlerCreation() {
    $controller = new ExamController();
    $controller->startExam($this->exam);
    $this->assertEquals($controller->getCurrentQuestionCount(), 1, "The first question is numbered with 1");
    $this->assertEquals($controller->getQuestionCount(), 10, "The are 10 questions in the current exam");
    $this->assertEquals($controller->getCurrentExam(), $this->exam);
  }

  public function testExamControllerQuestionNavigation() {
    $controller = new ExamController();
    $controller->startExam($this->exam);
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
    $controller = new ExamController();
    $controller->startExam($this->exam);
    $this->assertEquals($controller->getCurrentQuestion(), $this->questions[0]);

    $controller->moveToNextQuestion();
    $this->assertEquals($controller->getCurrentQuestion(), $this->questions[1]);

    $controller->moveToLastQuestion();
    $this->assertEquals($controller->getCurrentQuestion(), end($this->questions));
  }

  public function testReviewQuestionsLater() {
    $controller = new ExamController();

    $first_question = \Mockery::mock('Question');
    $first_question->shouldReceive('reviewLater')->with(TRUE);

    $second_question = \Mockery::mock('Question');
    $second_question->shouldReceive('reviewLater')->with(FALSE);
    $this->questions = array($first_question, $second_question);

    $controller->startExam($this->exam);

    // Mark the first question to be reviewed later
    $controller->markCurrentQuestionForLaterReview();

    // Mark the second question to not be reviewd later
    $controller->moveToNextQuestion();
    $controller->unmarkCurrentQuestionForLaterReview();
  }

  public function testFinalizeExam() {
    $controller = new ExamController();

    $this->exam->shouldReceive('finalize')->once();

    $controller->startExam($this->exam);
    $controller->finalizeExam();
  }


}
