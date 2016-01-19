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
        return $question->getAnswers() == array(2);
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
      ->setChoices($this->single_choice_question_answers)
      ->setRightChoices(array(2));

    $this->single_choice_question
      ->setTitle('Basic Math')
      ->setDescription('What is the result of 1+1?');

    $this->multiple_evaluator = \Mockery::mock('mdagostino\MultipleChoiceExams\Question\QuestionEvaluatorInterface');
    $this->multiple_evaluator
      ->shouldReceive('isCorrect')
      ->andReturnUsing(function($question) {
        return $question->getAnswers() == array('mercury', 'venus') ||
               $question->getAnswers() == array('venus', 'mercury');
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
      ->setChoices($this->multiple_choice_question_answers)
      ->setRightChoices(array('mercury', 'venus'));

    $this->multiple_choice_question
      ->setTitle('Planets')
      ->setDescription('What planets are between the Sun and the Earth?');
  }

  public function testQuestionCreation() {

    $this->assertEquals($this->single_choice_question->getTitle(), 'Basic Math');
    $this->assertEquals($this->single_choice_question->getDescription(), 'What is the result of 1+1?');
    $this->assertEquals($this->single_choice_question->getChoicesDescriptions(), $this->single_choice_question_answers);
    $this->assertFalse($this->single_choice_question->wasAnswered());

    $this->assertEquals($this->multiple_choice_question->getTitle(), 'Planets');
    $this->assertEquals($this->multiple_choice_question->getDescription(), 'What planets are between the Sun and the Earth?');
    $this->assertEquals($this->multiple_choice_question->getChoicesDescriptions(), $this->multiple_choice_question_answers);
    $this->assertFalse($this->multiple_choice_question->wasAnswered());
  }

  public function testQuestionTagging() {
    $this->single_choice_question->tag('tag 1');
    $this->single_choice_question->tag('tag 2');
    $this->single_choice_question->tag('tag 3');

    $this->assertTrue($this->single_choice_question->hasTag('tag 1'));
    $this->assertTrue($this->single_choice_question->hasTag('tag 2'));
    $this->assertFalse($this->single_choice_question->hasTag('tag 4'));

    $this->single_choice_question->unTag('tag 2');
    $this->assertTrue($this->single_choice_question->hasTag('tag 1'));
    $this->assertFalse($this->single_choice_question->hasTag('tag 2'));
    $this->assertTrue($this->single_choice_question->hasTag('tag 3'));
  }

  /**
   * @expectedException \Exception
   * @expectedExceptionMessage There is no method called inexistentMethod
   */
  public function testMagicMethods() {
    $info = new QuestionInfo();
    $question = new Question($this->single_evaluator, $info);

    $this->assertEquals($question->setTitle('test'), $question);
    $this->assertEquals($question->setChoices(['a' => 'A']), $question);
    $this->assertEquals($question->getTitle('test'), 'test');
    $this->assertEquals($question->getChoices(), ['a']);

    $question->inexistentMethod();
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

    $planet_question->answer(array('mercury', 'venus'));
    $this->assertTrue($planet_question->isCorrect());

    // Try with different order
    $planet_question->answer(array('venus', 'mercury'));
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
   * @expectedExceptionMessage You cannot use the keys (moon) as a valid choices. Valid choices are: (mercury, venus, earth, mars, jupiter, saturn, uranus, neptune)
   */
  public function testInvalidAnswer() {
    $planet_question = $this->multiple_choice_question;
    $planet_question->answer(array('moon'));
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\Exception\InvalidAnswerException
   * @expectedExceptionMessage You cannot use the keys (a, b) as a valid choices. Valid choices are: (mercury, venus, earth, mars, jupiter, saturn, uranus, neptune)
   */
  public function testInvalidChoicesKey() {
    $this->multiple_choice_question
      ->setChoices($this->multiple_choice_question_answers)
      ->setRightChoices(array('a', 'b'));
  }

  public function testCorrectCount() {
    $planet_question = $this->multiple_choice_question;

    $planet_question->answer(array('mercury', 'venus'));

    $planet_question->answer(array('mercury'));

    $planet_question->answer(array('mercury', 'mars'));

    $planet_question->answer(array('jupiter', 'mars', 'neptune'));

    $planet_question->answer(array('mercury', 'venus', 'jupiter', 'mars'));

    $math_question = $this->single_choice_question;

    $math_question->answer(array(2));
    $this->assertTrue($math_question->isCorrect());

    $math_question->answer(array(3));
    $this->assertFalse($math_question->isCorrect());
  }

}
