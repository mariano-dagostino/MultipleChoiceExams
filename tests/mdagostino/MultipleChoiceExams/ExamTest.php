<?php

namespace mdagostino\MultipleChoiceExams;

class ExamsTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function testExamCreation() {
    $exam = new Exam();
    $this->assertEquals($exam->questionsAnswered(), 0);
    $this->assertEquals($exam->totalQuestions(), 0);

    // Add ten questions to this exam
    $questions = array();
    for ($i=0; $i < 10; $i++) {
      $question = \Mockery::mock('Question');

      // Answer only the even questions.
      $question->shouldReceive('wasAnswered')->once()->andReturn($i % 2 == 0);
      $questions[] = $question;
    }

    $exam->setQuestions($questions);
    $this->assertEquals($exam->totalQuestions(), 10);
    // Five questions should be answered
    $this->assertEquals($exam->questionsAnswered(), 5);
  }

  public function testExamQuestionsToReviewLater() {
    $exam = new Exam();

    // At the begining of the exam there should not be answer to be reviewed later
    $this->assertFalse($exam->hasQuestionsToReview());

    // Add ten questions to this exam
    $questions = array();
    for ($i = 0; $i < 10; $i++) {
      $question = \Mockery::mock('Question');

      // Expect first five questions to be reviewed later
      $question->shouldReceive('reviewLater')->times($i < 5 ? 1 : 0)->andReturn();
      $question->shouldReceive('isMarkedToReviewLater')->andReturn($i < 5);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    // Mark the first questions to be reviewed later
    for ($id = 0; $id < 5; $id++) {
      $exam->reviewQuestionLater($id, TRUE);
    }

    // There should be some questions to be reviewed later
    $this->assertTrue($exam->hasQuestionsToReview());
  }


  public function testExamQuestionAnswering() {
    $exam = new Exam();

    // Add ten questions to this exam
    $questions = array();
    for ($i = 1; $i <= 10; $i++) {
      $question = \Mockery::mock('Question');

      // Expect question number 2 to be answered later.
      if ($i == 2) {
        $question->shouldReceive('answer')->once()->andReturn();
        $question->shouldReceive('wasAnswered')->once()->andReturn(TRUE);
      }
      else {
        // Other questions should not be answered
        $question->shouldReceive('answer')->times(0)->andReturn();
        $question->shouldReceive('wasAnswered')->once()->andReturn(FALSE);
      }
      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    // Questions are number starting by 0, the second question has 1 has ID.
    $exam->answerQuestion(1, array('my answer'));

    // Only one question should be answered
    $this->assertEquals($exam->questionsAnswered(), 1);
  }

  public function testExamRestart() {
    $exam = new Exam();

    // Add ten questions to this exam
    $questions = array();
    for ($i = 1; $i <= 10; $i++) {
      $question = \Mockery::mock('Question');

      $question->shouldReceive('forgotAnswer')->once();
      $question->shouldReceive('wasAnswered')->once();
      $questions[] = $question;
    }
    $exam->setQuestions($questions);
    $exam->reStart();

    $this->assertEquals($exam->questionsAnswered(), 0);
  }


}
