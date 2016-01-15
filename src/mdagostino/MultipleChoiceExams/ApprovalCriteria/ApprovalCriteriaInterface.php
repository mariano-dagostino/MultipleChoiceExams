<?php

namespace mdagostino\MultipleChoiceExams\ApprovalCriteria;

interface ApprovalCriteriaInterface {

  public function isApproved(array $questions);

  public function reset();

}
