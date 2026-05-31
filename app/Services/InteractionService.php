<?php

namespace App\Services;

use App\Support\DTOs\NormalizedEventData;

class InteractionService
{
    /**
     * @return array<string, mixed>
     */
    public function ingestNormalizedEvent(NormalizedEventData $eventData): array
    {
        return [
            'status' => 'queued_for_domain_processing',
            'event_key' => $eventData->eventKey,
            'event_type' => $eventData->eventType,
        ];
    }
}
