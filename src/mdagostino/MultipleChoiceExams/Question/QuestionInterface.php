<?php

namespace mdagostino\MultipleChoiceExams\Question;

interface QuestionInterface {

	public function __construct(QuestionEvaluatorInterface $question_evaluator);

	public function wasAnswered();

	public function answer(array $keys);

	public function resetAnswer();

	public function isCorrect();

	public function getChoices();

	public function setChoices(array $choices);

	public function getRightChoices();

	public function setRightChoices(array $choices);

	public function getAnswers();

	public function getQuestionEvaluator();
}



