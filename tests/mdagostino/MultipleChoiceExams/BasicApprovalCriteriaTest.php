<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\ApprovalCriteria\BasicApprovalCriteria;

class BasicApprovalCriteriaTest extends \PHPUnit_Framework_TestCase {

  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {
    $this->criteria = new BasicApprovalCriteria();
    $this->criteria->setSettings([
      'percent_to_approve_exam' => 75,
      'right_questions_sum' => 1.0,
      'wrong_questions_sum' => -0.3,
    ]);
  }

  public function testDefaultSettings() {
    $criteria = new BasicApprovalCriteria();

    $this->assertArrayHasKey('percent_to_approve_exam', $criteria->getSettings());
    $this->assertArrayHasKey('right_questions_sum', $criteria->getSettings());
    $this->assertArrayHasKey('wrong_questions_sum', $criteria->getSettings());
  }

  public function testRules() {
    $criteria = new BasicApprovalCriteria();

    $criteria->setSettings([
      'percent_to_approve_exam' => 75,
      'right_questions_sum' => 1.0,
      'unanswered_questions_sum' => 0,
      'wrong_questions_sum' => -0.3,
    ]);

    $rules = $this->criteria->rulesDescription();

    $this->assertEquals($rules[0], "The exam is approved with 75%");
    $this->assertEquals($rules[1], 'Correclty answered questions are considered +1.00');
    $this->assertEquals($rules[2], 'Wrong answered questions are considered -0.30');
    $this->assertEquals($rules[3], 'Unanswered questions are considered +0.00');

    $criteria->setSettings([
      'percent_to_approve_exam' => 75,
      'right_questions_sum' => 1.0,
      'unanswered_questions_sum' => 0,
      'wrong_questions_sum' => 0,
    ]);

    $rules = $criteria->rulesDescription();
    $this->assertEquals($rules, ["The exam is approved with 75%"]);
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
    $this->criteria = new BasicApprovalCriteria();
    $this->criteria->setSettings([
      'percent_to_approve_exam' => 93.5,
      'right_questions_sum' => 1.0,
      'wrong_questions_sum' => -0.3,
    ]);

    // Create 100 random questions
    $questions  = array();
    for ($i = 1; $i <= 100; $i++) {
      $question = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
      // Answer all the questions, correctly only 95 questions, incorrectly 5.
      $question->shouldReceive('wasAnswered')->andReturn(TRUE);
      $question->shouldReceive('isCorrect')->andReturn($i <= 95);
      $questions[] = $question;
    }

    // Exam not approved since 95 * 1 - 5 * 0,3 = 95.5
    $this->assertTrue($this->criteria->pass($questions));
    $this->assertEquals($this->criteria->getScore(), 93.5);
  }
}

