<?php
declare (strict_types=1);

namespace App;

use Temporal\Activity\ActivityMethod;

class ImageActivitiesImpl implements  ImageActivities {

    #[ActivityMethod(name: "detectChildren")]
    public function detectChildren(string $imageId): bool
    {
        $hasChildren = (bool)rand(0,1);
        echo "[Activity] Hình ảnh {$imageId} có con nít không? -> " . ($hasChildren ? 'CÓ' : 'KHÔNG') . "\n";
        return $hasChildren;
    }

    #[ActivityMethod(name: "notifyAdmin")]
    public function notifyAdmin(string $imageId): void
    {
        echo "[Activity] Đã gửi Notification cho Admin yêu cầu duyệt hình {$imageId}\n";
    }

    #[ActivityMethod(name: "updateStatus")]
    public function updateStatus(string $imageId, string $status): void
    {
        echo "[Activity] UPDATE DB: Trạng thái hình {$imageId} chuyển thành: {$status}\n";
    }
}