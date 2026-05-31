<?php

namespace App\Services;

class AutomationRunnerService
{
    /**
     * @return array<string, mixed>
     */
    public function runForInteraction(int $interactionId): array
    {
        return [
            'status' => 'pending_automation_matching',
            'interaction_id' => $interactionId,
        ];
    }
}
