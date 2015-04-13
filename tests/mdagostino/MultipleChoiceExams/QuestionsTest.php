<?php

namespace mdagostino\MultipleChoiceExams;

class QuestionsTest extends \PHPUnit_Framework_TestCase {

  protected $single_choice_question;
  protected $single_choice_question_answers;

  protected $multiple_choice_question;
  protected $multiple_choice_question_answers;


  public function setUp() {
    $this->single_choice_question = new Question();

    $this->single_choice_question_answers = array(
      1 => 'The result is 1',
      2 => 'The result is 2',
      3 => 'The result is 3',
      4 => 'The result is 4',
      5 => 'The result is 5',
    );

    $this->single_choice_question
    ->setTitle('Basic Math')
    ->setDescription('What is the result of 1+1?')
    ->setAvailableAnswers($this->single_choice_question_answers)
    ->setRightAnswers(array(2));

    $this->multiple_choice_question = new Question();

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
    ->setTitle('Planets')
    ->setDescription('What planets are between the Sun and the Earth?')
    ->setAvailableAnswers($this->multiple_choice_question_answers)
    ->setRightAnswers(array('mercury', 'venus'));
  }

  public function testQuestionCreation() {

    $this->assertEquals($this->single_choice_question->getTitle(), 'Basic Math');
    $this->assertEquals($this->single_choice_question->getDescription(), 'What is the result of 1+1?');
    $this->assertEquals($this->single_choice_question->getAvailableAnswers(), $this->single_choice_question_answers);
    $this->assertFalse($this->single_choice_question->wasAnswered());

    $this->assertEquals($this->multiple_choice_question->getTitle(), 'Planets');
    $this->assertEquals($this->multiple_choice_question->getDescription(), 'What planets are between the Sun and the Earth?');
    $this->assertEquals($this->multiple_choice_question->getAvailableAnswers(), $this->multiple_choice_question_answers);
    $this->assertFalse($this->multiple_choice_question->wasAnswered());

    $question = new Question();
    $question->setInternalId(4);
    $this->assertEquals($question->getInternalId(), 4);

    $question->setTopic(array('planets' => 'Planets related questions'));
    $this->assertEquals($question->getTopic(), array('planets' => 'Planets related questions'));

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
    $this->assertFalse($planet_question->isCorrect());

    $planet_question->answer(array('mercury', 'venus'));
    $this->assertTrue($planet_question->isCorrect());

    // Try with different order
    $planet_question->answer(array('venus', 'mercury'));
    $this->assertTrue($planet_question->isCorrect());
  }

  public function testForgotAnswer() {
    $planet_question = $this->multiple_choice_question;

    $this->assertFalse($planet_question->wasAnswered());

    $planet_question->answer(array('mercury', 'venus'));

    $this->assertTrue($planet_question->wasAnswered());
    $this->assertTrue($planet_question->isCorrect());

    $planet_question->forgotAnswer();

    $this->assertFalse($planet_question->wasAnswered());
    $this->assertFalse($planet_question->isCorrect());
  }

  /**
   * @expectedException mdagostino\MultipleChoiceExams\InvalidQuestionException
   * @expectedExceptionMessage The key moon is not a valid answer for the question Planets
   */
  public function testInvalidAnswer() {
    $planet_question = $this->multiple_choice_question;
    $planet_question->answer(array('moon'));
  }


  public function testReviewLater() {
    $planet_question = $this->multiple_choice_question;

    $this->assertFalse($planet_question->wasAnswered());
    $this->assertFalse($planet_question->isMarkedToReviewLater());

    $planet_question->reviewLater(TRUE);
    $this->assertTrue($planet_question->isMarkedToReviewLater());

    $planet_question->reviewLater(FALSE);
    $this->assertFalse($planet_question->isMarkedToReviewLater());
  }

  public function testCorrectPercent() {
    $planet_question = $this->multiple_choice_question;

    $planet_question->answer(array('mercury', 'venus'));
    $this->assertEquals($planet_question->correctPercent(), 100);

    $planet_question->answer(array('mercury'));
    $this->assertEquals($planet_question->correctPercent(), 50);

    $planet_question->answer(array('mercury', 'mars'));
    $this->assertEquals($planet_question->correctPercent(), 0);

    $planet_question->answer(array('jupiter', 'mars', 'neptune'));
    $this->assertEquals($planet_question->correctPercent(), 0);

    $planet_question->answer(array('mercury', 'venus', 'jupiter', 'mars'));
    $this->assertEquals($planet_question->correctPercent(), 0);

    $math_question = $this->single_choice_question;

    $math_question->answer(array(2));
    $this->assertEquals($math_question->correctPercent(), 100);

    $math_question->answer(array(3));
    $this->assertEquals($math_question->correctPercent(), 0);
  }

}
