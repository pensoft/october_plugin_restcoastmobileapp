<?php
namespace Pensoft\RestcoastMobileApp\Listeners;

use Illuminate\Queue\QueueManager;

trait UseSyncQueue {
    public function queue(QueueManager $handler, $method, $arguments)
    {
        return $handler->push($method, $arguments,'sync-cdn');
    }
}
