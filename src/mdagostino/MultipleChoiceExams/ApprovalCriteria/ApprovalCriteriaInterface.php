<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

interface ApprovalCriteriaInterface {

  public function pass(array $questions);

}
