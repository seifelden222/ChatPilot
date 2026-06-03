<?php

namespace App\Listeners;

use App\Events\OutgoingActionCreated;
use App\Jobs\ExecuteOutgoingActionJob;

class QueueOutgoingActionExecution
{
    public function handle(OutgoingActionCreated $event): void
    {
        ExecuteOutgoingActionJob::dispatch($event->outgoingActionId);
    }
}
