<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InteractionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $interactionId,
        public int $workspaceId,
        public int $channelId,
        public int $threadId,
        public int $actorId,
        public string $occurredAt,
    ) {}
}
