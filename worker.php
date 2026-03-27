<?php
require __Dir__ . "/vendor/autoload.php";

use Temporal\WorkerFactory;
use App\ImageApprovalWorkflowImpl;
use App\ImageActivitiesImpl;

$factory = WorkerFactory::create();
$worker = $factory->newWorker();

$worker->registerWorkflowTypes(ImageApprovalWorkflowImpl::class);
$worker->registerActivity(ImageActivitiesImpl::class);

$factory->run();