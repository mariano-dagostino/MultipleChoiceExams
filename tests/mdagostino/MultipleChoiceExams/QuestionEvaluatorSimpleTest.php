<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorSimple;

class QuestionEvaluatorSimpleTest extends \PHPUnit_Framework_TestCase {

  public function testExamCreation() {
    $reflection = new \ReflectionClass('mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorSimple');
    $questionHitCountMethod = $reflection->getMethod('questionHitCount');
    $questionHitCountMethod->setAccessible(TRUE);
    $questionMissCountMethod = $reflection->getMethod('questionMissCount');
    $questionMissCountMethod->setAccessible(TRUE);
    $evaluator = new QuestionEvaluatorSimple();

    $question1 = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');

    $question1
      ->shouldReceive('getChoices')->andReturn(['A', 'B', 'C', 'D', 'E', 'F'])
      ->shouldReceive('getRightChoices')->andReturn(['A', 'B'])
      ->shouldReceive('getAnswers')->andReturn(['A', 'B', 'C']);

    $hit_count = $questionHitCountMethod->invokeArgs($evaluator, array($question1));
    $miss_count = $questionMissCountMethod->invokeArgs($evaluator, array($question1));
    $this->assertEquals($hit_count, 2);
    $this->assertEquals($miss_count, 1);
    $this->assertFalse($evaluator->isCorrect($question1));

    $question2 = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionInterface');
    $question2
      ->shouldReceive('getChoices')->andReturn(['A', 'B', 'C', 'D', 'E', 'F'])
      ->shouldReceive('getRightChoices')->andReturn(['A', 'B', 'C'])
      ->shouldReceive('getAnswers')->andReturn(['A', 'B', 'C']);

    $hit_count = $questionHitCountMethod->invokeArgs($evaluator, array($question2));
    $miss_count = $questionMissCountMethod->invokeArgs($evaluator, array($question2));
    $this->assertEquals($hit_count, 3);
    $this->assertEquals($miss_count, 0);
    $this->assertTrue($evaluator->isCorrect($question2));
  }
}
