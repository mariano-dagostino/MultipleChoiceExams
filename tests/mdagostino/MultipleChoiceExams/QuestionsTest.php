<?php

namespace mdagostino\MultipleChoiceExams;

use mdagostino\MultipleChoiceExams\Question\Question;
use mdagostino\MultipleChoiceExams\Question\QuestionInfo;

class QuestionsTest extends \PHPUnit_Framework_TestCase {

  protected $single_choice_question;
  protected $single_choice_question_answers;

  protected $multiple_choice_question;
  protected $multiple_choice_question_answers;


  public function tearDown() {
    \Mockery::close();
  }

  public function setUp() {
    $this->single_evaluator = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorInterface');
    $this->single_evaluator
      ->shouldReceive('isCorrect')
      ->andReturnUsing(function($question) {
        return $question->hitCount() == 1 && $question->missCount() == 0;
      });


    $this->single_choice_question_info = new QuestionInfo();
    $this->single_choice_question = new Question($this->single_evaluator, $this->single_choice_question_info);

    $this->single_choice_question_answers = array(
      1 => 'The result is 1',
      2 => 'The result is 2',
      3 => 'The result is 3',
      4 => 'The result is 4',
      5 => 'The result is 5',
    );

    $this->single_choice_question
      ->setAnswers($this->single_choice_question_answers, array(2));

    $this->single_choice_question
      ->getInfo()
        ->setTitle('Basic Math')
        ->setDescription('What is the result of 1+1?');

    $this->multiple_evaluator = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorInterface');
    $this->multiple_evaluator
      ->shouldReceive('isCorrect')
      ->andReturnUsing(function($question) {
        return $question->hitCount() == 2 && $question->missCount() == 0;
      });

    $this->multiple_choice_question_info = new QuestionInfo();
    $this->multiple_choice_question = new Question($this->multiple_evaluator, $this->multiple_choice_question_info);

    $this->multiple_choice_question_answers = array(
      'mercury' => 'Mercury',
      'venus' => 'Venus',
      'earth' => 'Earth',
      'mars' => 'Mars',
      'jupiter' => 'Jupiter',
      'saturn' => 'Saturn',
      'uranus' => 'Uranus',
      'neptune' => 'Neptune',
    );

    $this->multiple_choice_question
      ->setAnswers($this->multiple_choice_question_answers, array('mercury', 'venus'));

    $this->multiple_choice_question
      ->getInfo()
        ->setTitle('Planets')
        ->setDescription('What planets are between the Sun and the Earth?');
  }

  public function testQuestionCreation() {

    $this->assertEquals($this->single_choice_question->getInfo()->getTitle(), 'Basic Math');
    $this->assertEquals($this->single_choice_question->getInfo()->getDescription(), 'What is the result of 1+1?');
    $this->assertEquals($this->single_choice_question->getAvailableAnswers(), $this->single_choice_question_answers);
    $this->assertFalse($this->single_choice_question->wasAnswered());

    $this->assertEquals($this->multiple_choice_question->getInfo()->getTitle(), 'Planets');
    $this->assertEquals($this->multiple_choice_question->getInfo()->getDescription(), 'What planets are between the Sun and the Earth?');
    $this->assertEquals($this->multiple_choice_question->getAvailableAnswers(), $this->multiple_choice_question_answers);
    $this->assertFalse($this->multiple_choice_question->wasAnswered());
  }

  public function testQuestionCorrect() {
    $math_question = $this->single_choice_question;

    // At the begining the question is not correct by default
    $this->assertFalse($math_question->isCorrect());
    // Also it should not be already answered
    $this->assertFalse($math_question->wasAnswered());

    // Wrong answer
    $math_question->answer(array(5));
    $this->assertFalse($math_question->isCorrect());
    $this->assertTrue($math_question->wasAnswered());

    // Correct answer
    $math_question->answer(array(2));
    $this->assertTrue($math_question->isCorrect());


    $planet_question = $this->multiple_choice_question;

    // At the begining the question is not correct by default
    $this->assertFalse($planet_question->isCorrect());
    // Also it should not be already answered
    $this->assertFalse($planet_question->wasAnswered());

    $planet_question->answer(array('jupiter'));
    $this->assertFalse($planet_question->isCorrect());
    $this->assertTrue($planet_question->wasAnswered());

    // Only one answer make the Question incorrect
    $planet_question->answer(array('mercury'));
    $this->assertEquals($planet_question->hitCount(), 1);
    $this->assertEquals($planet_question->missCount(), 0);

    $planet_question->answer(array('mercury', 'venus'));
    $this->assertEquals($planet_question->hitCount(), 2);
    $this->assertEquals($planet_question->missCount(), 0);
    $this->assertTrue($planet_question->isCorrect());

    // Try with different order
    $planet_question->answer(array('venus', 'mercury'));
    $this->assertEquals($planet_question->hitCount(), 2);
    $this->assertEquals($planet_question->missCount(), 0);
    $this->assertTrue($planet_question->isCorrect());
  }

  public function testResetAnswer() {
    $planet_question = $this->multiple_choice_question;

    $this->assertFalse($planet_question->wasAnswered());

    $planet_question->answer(array('mercury', 'venus'));

    $this->assertTrue($planet_question->wasAnswered());
    $this->assertTrue($planet_question->isCorrect());

    $planet_question->resetAnswer();

    $this->assertFalse($planet_question->wasAnswered());
    $this->assertFalse($planet_question->isCorrect());
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\Exception\InvalidAnswerException
   * @expectedExceptionMessage The key 'moon' is not a valid answer for the question 'Planets'
   */
  public function testInvalidAnswer() {
    $planet_question = $this->multiple_choice_question;
    $planet_question->answer(array('moon'));
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\Exception\InvalidAnswerException
   * @expectedExceptionMessage The key 'invalid key' is not a valid answer for the question 'Planets'
   */
  public function testInvalidAnswerKey() {
    $this->multiple_choice_question
      ->setAnswers($this->multiple_choice_question_answers, array('invalid key'));
  }

  public function testCorrectCount() {
    $planet_question = $this->multiple_choice_question;

    $planet_question->answer(array('mercury', 'venus'));
    $this->assertEquals($planet_question->hitCount(), 2);

    $planet_question->answer(array('mercury'));
    $this->assertEquals($planet_question->hitCount(), 1);

    $planet_question->answer(array('mercury', 'mars'));
    $this->assertEquals($planet_question->hitCount(), 1);
    $this->assertEquals($planet_question->missCount(), 1);

    $planet_question->answer(array('jupiter', 'mars', 'neptune'));
    $this->assertEquals($planet_question->hitCount(), 0);

    $planet_question->answer(array('mercury', 'venus', 'jupiter', 'mars'));
    $this->assertEquals($planet_question->hitCount(), 2);
    $this->assertEquals($planet_question->missCount(), 2);

    $math_question = $this->single_choice_question;

    $math_question->answer(array(2));
    $this->assertTrue($math_question->isCorrect());

    $math_question->answer(array(3));
    $this->assertFalse($math_question->isCorrect());
  }

}
