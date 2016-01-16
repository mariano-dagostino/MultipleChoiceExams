<?php

namespace mdagostino\MultipleChoiceExams\Question;

interface QuestionInterface {

	public function __construct(QuestionEvaluatorInterface $question_evaluator);

	public function wasAnswered();

	public function answer(array $keys);

	public function resetAnswer();

	public function isCorrect();

	public function setChoices(array $answers, array $right_answers);

	public function getChoices();

	public function getRightChoices();

	public function getAnswers();

	public function getQuestionEvaluator();
}



