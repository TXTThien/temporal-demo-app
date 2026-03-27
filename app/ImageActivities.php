<?php
declare (strict_types=1);

namespace App;

use Temporal\Activity\ActivityInterface;
use Temporal\Activity\ActivityMethod;

#[ActivityInterface]
interface ImageActivities
{
    #[ActivityMethod(name: "detectChildren")]
    public function detectChildren(string $imageId): bool;

    #[ActivityMethod(name: "notifyAdmin")]
    public function notifyAdmin(string $imageId): void;

    #[ActivityMethod(name: "updateStatus")]
    public function updateStatus(string $imageId, string $status): void;
}