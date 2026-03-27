<?php
declare (strict_types=1);

namespace App;

use Carbon\CarbonInterval;
use Temporal\Workflow;
use Temporal\Activity\ActivityOptions;
use Temporal\Workflow\SignalMethod;
use Temporal\Workflow\WorkflowMethod;

class ImageApprovalWorkflowImpl implements ImageApprovalWorkflow {
    private ?string $adminDecision = null;
    #[WorkflowMethod]
    public function processImage(string $imageId)
    {
        $activities = Workflow::newActivityStub(
            ImageActivities::class,
            ActivityOptions::new()->withStartToCloseTimeout(10)->withScheduleToStartTimeout(CarbonInterval::minute(5))
        );
        $hasChildren = yield $activities->detectChildren($imageId);

        if ($hasChildren) {
            yield $activities->notifyAdmin($imageId);
            yield Workflow::await(fn() => $this->adminDecision !== null);
            yield $activities->updateStatus($imageId, $this->adminDecision);
            return "Quy trình kết thúc (Admin đã xử lý: {$this->adminDecision})";
        }
        else {
            yield $activities->updateStatus($imageId, 'approved');
            return "Quy trình kết thúc (Tự động duyệt do không có con nít)";
        }
    }

    #[SignalMethod]
    public function adminReview(string $decision)
    {
        $this->adminDecision = $decision;
    }
}