<?php

namespace mdagostino\MultipleChoiceExams;

class IntegrationExamTest extends \PHPUnit_Framework_TestCase {

  public function testExamBasicWorkflow() {

    $exam = new Exam();

    $exam->setDuration(30);

    $examTimer = \Mockery::mock('ExamTimer');
    // The time is in seconds
    $examTimer->shouldReceive('getTime')->andReturn(0, 1000, 1600);

    $exam->setTimer($examTimer);

    for ($i=0; $i < 100; $i++) {

      $available_answers = array('one', 'two', 'three');
      $right_answers = array('one', 'three');

      $question = new Question();
      $question
        ->setTitle('Question ' . $i)
        ->setDescription('Description for question ' . $i)
        ->setAvailableAnswers($available_answers)
        ->setRightAnswers($right_answers);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $exam->start();

    $exam->answerQuestion(0, array('one'));
    $exam->answerQuestion(1, array('one', 'two'));
    $exam->answerQuestion(3, array('one', 'three'));

    $exam->finalize();

    $this->assertFalse($exam->isApproved());
  }

  public function testExamApproved() {

    $exam = new Exam();

    for ($i=0; $i < 100; $i++) {

      $available_answers = array('one', 'two', 'three');
      $right_answers = array('one', 'three');

      $question = new Question();
      $question
        ->setTitle('Question ' . $i)
        ->setDescription('Description for question ' . $i)
        ->setAvailableAnswers($available_answers)
        ->setRightAnswers($right_answers);

      $questions[] = $question;
    }
    $exam->setQuestions($questions);

    $exam->start();

    // Answer correctly 70% of the answers
    for ($i=0; $i < 70; $i++) {
      $exam->answerQuestion($i, array('one', 'three'));
    }

    $exam->finalize();

    $this->assertTrue($exam->isApproved());
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\ExpiredTimeException
   * @expectedExceptionMessage There is no time left for the next question
   */
  public function testTimeLeftNegative() {

      // This test checks if the questions are not available to be answered  
      // when the time to complete the exam is over the duration of the exam.
      $exam = new Exam();

      $exam->setDuration(30);

      $examTimer = \Mockery::mock('ExamTimer');
      // The time is in seconds
      $examTimer->shouldReceive('getTime')->andReturn(0, 1000, 1769, 1801);

      $exam->setTimer($examTimer);

      for ($i=0; $i < 10; $i++) {

        $available_answers = array('one', 'two', 'three');
        $right_answers = array('one', 'three');

        $question = new Question();
        $question
          ->setTitle('Question ' . $i)
          ->setDescription('Description for question ' . $i)
          ->setAvailableAnswers($available_answers)
          ->setRightAnswers($right_answers);

        $questions[] = $question;
      }
      $exam->setQuestions($questions);

      $exam->start();

      $exam->answerQuestion(0, array('one'));
      $exam->answerQuestion(1, array('one', 'two'));
      $exam->answerQuestion(2, array('one', 'three'));
      $exam->answerQuestion(3, array('one', 'three'));

      $exam->finalize();

      $this->assertFalse($exam->isApproved());

    }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\ExpiredTimeException
   * @expectedExceptionMessage There is no time left for the next question
   */
  public function testTimeLeftZero() {

      // This test checks if the questions are not available to be answered 
      // when the time to complete the exam is exactly the duration of the exam.
      $exam = new Exam();

      $exam->setDuration(30);

      $examTimer = \Mockery::mock('ExamTimer');
      // The time is in seconds
      $examTimer->shouldReceive('getTime')->andReturn(0, 1000, 1800);

      $exam->setTimer($examTimer);

      for ($i=0; $i < 10; $i++) {

        $available_answers = array('one', 'two', 'three');
        $right_answers = array('one', 'three');

        $question = new Question();
        $question
          ->setTitle('Question ' . $i)
          ->setDescription('Description for question ' . $i)
          ->setAvailableAnswers($available_answers)
          ->setRightAnswers($right_answers);

        $questions[] = $question;
      }
      $exam->setQuestions($questions);

      $exam->start();

      $exam->answerQuestion(0, array('one'));
      $exam->answerQuestion(1, array('one', 'two'));
      $exam->answerQuestion(2, array('one', 'three'));

      $exam->finalize();

      $this->assertFalse($exam->isApproved());

    }  
  
}
