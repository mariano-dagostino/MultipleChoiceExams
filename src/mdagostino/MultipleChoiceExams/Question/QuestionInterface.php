<?php

namespace mdagostino\MultipleChoiceExams\Question;

interface QuestionInterface {

	public function __construct(QuestionEvaluatorInterface $question_evaluator);

	public function wasAnswered();

	public function correctlyChossenCount();

	public function rightChoicesCount();

	public function totalChoicesCount();

	public function answer($keys);

	public function resetAnswer();

	public function isCorrect();

	public function setAnswers($answers, $right_answers);

	public function getAvailableAnswers();

	public function getRightAnswers();

	public function getChossenAnswers();


}



