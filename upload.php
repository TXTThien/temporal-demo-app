<?php
require __DIR__ . '/vendor/autoload.php';

use Temporal\Client\WorkflowClient;
use Temporal\Client\GRPC\ServiceClient;
use Temporal\Client\WorkflowOptions;
use App\ImageApprovalWorkflow;

$client = WorkflowClient::create(ServiceClient::create('127.0.0.1:7233'));

$imageId = "IMG_" . rand(1000, 9999);

echo "FE: Lưu DB hình ảnh {$imageId} với status = 'waiting_for_approve'\n";

$workflow = $client->newWorkflowStub(
    ImageApprovalWorkflow::class,
    WorkflowOptions::new()->withWorkflowId('process-image-' . $imageId)
);

$client->start($workflow, $imageId);

echo "FE: Upload thành công! Trả về giao diện cho user.\n";