<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OutgoingActionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $outgoingActionId,
        public int $workspaceId,
        public int $channelId,
        public int $automationRunId,
        public int $interactionId,
    ) {}
}
