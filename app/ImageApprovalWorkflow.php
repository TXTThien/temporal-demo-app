<?php
declare (strict_types=1);

namespace App;

use Temporal\Workflow\WorkflowInterface;
use Temporal\Workflow\WorkflowMethod;
use Temporal\Workflow\SignalMethod;
#[WorkflowInterface]
interface ImageApprovalWorkflow{
    #[WorkflowMethod]
    public function processImage(string $imageId);

    #[SignalMethod]
    public function adminReview (string $decision);
}