<?php

namespace App\Actions;

use App\Models\Actor;

class UpsertActorAction
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function handle(int $workspaceId, string $displayName, array $metadata = []): Actor
    {
        return Actor::query()->create([
            'workspace_id' => $workspaceId,
            'display_name' => $displayName,
            'actor_type' => 'person',
            'metadata' => $metadata,
        ]);
    }
}
