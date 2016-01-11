<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\PositiveNegativeApprovalCriteria;

class PositiveNegativeApprovalCriteriaTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function testDefaultSettings() {
    $criteria = new PositiveNegativeApprovalCriteria();

    $this->assertArrayHasKey('approval_percent_exam', $criteria->getSettings());
  }

  public function testMinimunRequiredQuestions() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $questions[] = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
    }
    $criteria->setQuestions($questions);

    // Check that 30 questions are required since 30 is the 60% of 50 questions.
    $this->assertEquals($criteria->questionsRequiredToPass(), 30);

    // Now change the settings
    $criteria->setSettings(array('approval_percent_exam' => 50));

    // Check that 25 questions are required since 25 is the 50% of 50 questions.
    $this->assertEquals($criteria->questionsRequiredToPass(), 25);
  }

  public function testExamFailed() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correctly only 29 questions. 30 questions are required to pass
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i < 29);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam not approved.
    $this->assertFalse($criteria->pass($questions));
  }


  public function testExamFailedDueWrongAnswers() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Anwser incorrectly the first 15 questions
      // Answer correcty the last 35 questions
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i > 34);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam failed because the wrong answers are considered negative
    // 35 - 15 = 20, minimun required to pass 30.
    $this->assertFalse($criteria->pass($questions));
  }

  public function testExamApprovedNoWrongAnswers() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correcty the last 40 questions
      // Leave the rest without answers
      $question->shouldReceive('wasAnswered')->andReturn($i >= 10);
      $question->shouldReceive('isCorrect')->andReturn($i > 9);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam approved.
    // Since only are considered the anwsered questions
    $this->assertTrue($criteria->pass($questions));
  }

  public function testExamApproved() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correcty the last 40 questions
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->once()->andReturn($i > 9);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam not approved.
    $this->assertTrue($criteria->pass($questions));
  }

  public function testExamApprovedWithMinimunMark() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correcty the last 40 questions
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      // Answer incorrectly the first ten
      $question->shouldReceive('isCorrect')->once()->andReturn($i > 9);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam approved since
    // 40 correct - 10 incorrect = 30 correct, minumun required mark
    $this->assertTrue($criteria->pass($questions));
  }

  public function testExamApprovedUnAnsweredQuestions() {
    $criteria = new PositiveNegativeApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Do not answer the first 20 answers
      $question->shouldReceive('wasAnswered')->andReturn($i >= 20);
      // Answer correcty the last 30 questions
      $question->shouldReceive('isCorrect')->andReturn($i > 19);
      $questions[] = $question;
    }
    $criteria->setQuestions($questions);

    // Exam approved.
    $this->assertTrue($criteria->pass($questions));
  }

}

