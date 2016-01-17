<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Exam\Exam;
use mdagostino\MultipleChoiceExams\Exception\InvalidQuestionException;

class ExamsTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function testExamCreation() {

    $exam = new Exam();

    $this->assertEquals($exam->questionsAnsweredCount(), 0);
    $this->assertEquals($exam->getQuestionCount(), 0);

    // Add ten questions to this exam
    $questions = array();
    for ($i=0; $i < 10; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');

      // Answer only the even questions.
      $question->shouldReceive('wasAnswered')->once()->andReturn($i % 2 == 0);
      $questions[] = $question;
    }

    $exam->setQuestions($questions);
    $this->assertEquals($exam->getQuestionCount(), 10);
    // Five questions should be answered
    $this->assertEquals($exam->questionsAnsweredCount(), 5);
  }

  public function testExamQuestionAnswering() {
    $exam = new Exam();

    // Add ten questions to this exam
    $questions = array();
    for ($i = 1; $i <= 10; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');

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
    $this->assertEquals($exam->questionsAnsweredCount(), 1);
  }


  public function testGetSpecificQuestion() {
    $exam = new Exam();

    // Add ten questions to this exam
    $questions = array();
    for ($i = 1; $i <= 10; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $this->assertEquals($exam->getQuestion(3), $questions[3]);
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\Exception\InvalidQuestionException
   * @expectedExceptionMessage There is no question with id 3
   */
  public function testGetInvalidQuestion() {
    $exam = new Exam();

    $this->assertEquals($exam->getQuestion(3), $questions[3]);
  }

}
