<?php

namespace mdagostino\MultipleChoiceExams;

interface QuestionInterface {

public function wasAnswered();

public function correctPercent();

public function isMarkedToReviewLater();

public function reviewLater($review_later);

public function answer($keys);

public function forgotAnswer();

public function validKeys();

public function setTitle($title);

public function getTitle();

public function setInternalId($internal_id);

public function getInternalId();

public function setDescription($description);

public function getDescription();

public function setTopic($topic);

public function getTopic();

public function setAvailableAnswers($answers);

public function getAvailableAnswers();

public function setRightAnswers($answers);

public function getRightAnswers();

}



