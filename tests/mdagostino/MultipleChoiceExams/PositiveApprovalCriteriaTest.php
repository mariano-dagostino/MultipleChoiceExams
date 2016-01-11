<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\PositiveApprovalCriteria;

class PositiveApprovalCriteriaTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function testDefaultSettings() {
    $criteria = new PositiveApprovalCriteria();

    $this->assertArrayHasKey('approval_percent_exam', $criteria->getSettings());
  }

  public function testMinimunRequiredQuestions() {
    $criteria = new PositiveApprovalCriteria();

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
    $criteria = new PositiveApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correctly only 29 questions. 30 questions are required to pass
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i < 28);
      $questions[] = $question;
    }

    // Exam not approved.
    $this->assertFalse($criteria->pass($questions));
  }

  public function testExamApproved() {
    $criteria = new PositiveApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correcty the last 40 questions
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i > 9);
      $questions[] = $question;
    }

    // Exam approved.
    $this->assertTrue($criteria->pass($questions));
  }

  public function testExamApprovedWithMinimunMark() {
    $criteria = new PositiveApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer correcty the last 30 questions
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i >= 20);
      $questions[] = $question;
    }

    // Exam approved.
    $this->assertTrue($criteria->pass($questions));
  }

  public function testExamApprovedUnAnsweredQuestions() {
    $criteria = new PositiveApprovalCriteria();

    // Create 50 random questions
    $questions  = array();
    for ($i=0; $i < 50; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Do not answer the first 20 answers
      $question->shouldReceive('wasAnswered')->andReturn($i >= 20);
      // Answer correcty the last 30 questions
      $question->shouldReceive('isCorrect')->andReturn($i >= 20);
      $questions[] = $question;
    }

    // Exam approved.
    $this->assertTrue($criteria->pass($questions));
  }

}
