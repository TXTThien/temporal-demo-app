<?php
require __DIR__ . '/vendor/autoload.php';

use Temporal\Client\WorkflowClient;
use Temporal\Client\GRPC\ServiceClient;

$client = WorkflowClient::create(ServiceClient::create('127.0.0.1:7233'));

$imageId = 'IMG_5455';

echo "Admin đang bấm nút Duyệt (Approve) cho hình {$imageId}...\n";

$workflow = $client->newUntypedRunningWorkflowStub('process-image-' . $imageId);

$workflow->signal('adminReview', 'approved');
echo "Đã gửi tín hiệu Duyệt thành công!\n";