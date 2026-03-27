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
            $version = yield Workflow::getVersion('add-slack-notification',Workflow::DEFAULT_VERSION,1);
            $bugFixVersion = yield Workflow::getVersion('bug-fix-version',Workflow::DEFAULT_VERSION,1);
            $addOutputVersion = yield Workflow::getVersion('add-output-version',Workflow::DEFAULT_VERSION,1);

            if ($version ===1)
            {
                yield $activities->sendSlackMessage($imageId);
            }
            yield $activities->notifyAdmin($imageId);
            yield Workflow::await(fn() => $this->adminDecision !== null);

            if ($bugFixVersion ===  Workflow::DEFAULT_VERSION) {
                yield $activities->updateStatus($imageId,'waiting');
            }
            else{
                yield $activities->updateStatus($imageId, $this->adminDecision);
            }

            if ($addOutputVersion ===  Workflow::DEFAULT_VERSION) {
                return "Quy trình kết thúc (Admin đã xử lý: {$this->adminDecision})";
            }
            else{
                return [
                    'image_id' => $imageId,
                    'final_decision' => $this->adminDecision,
                    'message' => 'Quy trình hoàn tất thành công'
                ];
            }
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