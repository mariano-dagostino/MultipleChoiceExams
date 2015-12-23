#MultipleChoiceExams

The idea of this repository is adapt to different CMS as Drupal, so they only have the responsability of managing the content of the database.

MultipleChoiceExams is a set of PHP clases to manage Multiple Choice Exams.

#Example
This is how the code works: 


```php
namespace mdagostino\MultipleChoiceExams;

class IntegrationExamTest extends \PHPUnit_Framework_TestCase {

  public function testExamBasicWorkflow() {

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

    $exam->answerQuestion(0, array('one'));
    $exam->answerQuestion(1, array('one', 'two'));
    $exam->answerQuestion(3, array('one', 'three'));

    $exam->finalize();

    if ($this->assertFalse($exam->isApproved())= TRUE) {
      return "Approved exam";
      };
  }
``` 
[![Build Status](https://travis-ci.org/mariano-dagostino/MultipleChoiceExams.svg?branch=master)](https://travis-ci.org/mariano-dagostino/MultipleChoiceExams)
