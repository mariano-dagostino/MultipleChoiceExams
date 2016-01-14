<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\BasicApprovalCriteria;

class BasicApprovalCriteriaTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {
    $this->criteria = new BasicApprovalCriteria();
    $this->criteria
      ->setScoreToApprove(75)
      ->setRightQuestionsSum(1)
      ->setWrongQuestionsRest(0.3);
  }

  public function testExamFailedDueWrongAnswers() {
    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer all the questions, correctly only 80 questions, incorrectly 20.
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i <= 80);
      $questions[] = $question;
    }

    // Exam not approved since 80 * 1 - 20 * 0,3 = 74
    $this->assertFalse($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 74);
  }

  public function testInBlankExam() {
    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer all the questions, correctly only 80 questions, incorrectly 20.
      $question->shouldReceive('wasAnswered')->andReturn(FALSE);
      $questions[] = $question;
    }

    $this->assertFalse($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 0);
  }

  public function testNegativeScoreExam() {
    $this->criteria
    ->setScoreToApprove(60)
    ->setWrongQuestionsRest(1.0)
    ->setUnansweredQuestionsRest(0.5);

    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer 50% of the questions, correctly only 25 questions, incorrectly 25.
      $question->shouldReceive('wasAnswered')->andReturn($i <= 50);
      $question->shouldReceive('isCorrect')->andReturn($i <= 25);
      $questions[] = $question;
    }

    $this->assertFalse($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 0);
  }


  public function testExamApprovedNoWrongAnswers() {
    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer only 80 questions.
      $question->shouldReceive('wasAnswered')->andReturn($i <= 80);
      // All the answered questions were correct.
      $question->shouldReceive('isCorrect')->andReturn($i <= 80);
      $questions[] = $question;
    }

    // Exam is approved since 80 * 1 - 0 * 0,3 = 80
    $this->assertTrue($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 80);
  }

  public function testExamApprovedWithMinimunMark() {
    $this->criteria->setScoreToApprove(93.5);

    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer all the questions, correctly 95 questions, incorrectly 5.
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i <= 95);
      $questions[] = $question;
    }

    // Exam not approved since 95 * 1 - 5 * 0,3 = 93.5
    $this->assertTrue($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 93.5);
  }
}

