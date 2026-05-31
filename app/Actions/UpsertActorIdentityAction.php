<?php

namespace App\Actions;

use App\Models\ActorIdentity;

class UpsertActorIdentityAction
{
    /**
     * @param  array<string, mixed>  $identityData
     */
    public function handle(
        int $workspaceId,
        int $channelId,
        int $actorId,
        string $identityKey,
        array $identityData = [],
    ): ActorIdentity {
        return ActorIdentity::query()->updateOrCreate(
            [
                'workspace_id' => $workspaceId,
                'channel_id' => $channelId,
                'identity_key' => $identityKey,
            ],
            [
                'actor_id' => $actorId,
                'identity_data' => $identityData,
            ],
        );
    }
}
