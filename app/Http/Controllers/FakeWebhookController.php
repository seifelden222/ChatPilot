<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessWebhookEventJob;
use App\Models\Channel;
use App\Models\WebhookEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FakeWebhookController extends Controller
{
    public function __invoke(Request $request, string $channel): JsonResponse
    {
        $validator = Validator::make($request->json()->all(), [
            'event_id' => ['required', 'string'],
            'event_type' => ['required', 'string'],
            'occurred_at' => ['required', 'date'],
            'actor.identity' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'rejected',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        $channelModel = Channel::query()
            ->where('external_id', $channel)
            ->orWhere('id', $channel)
            ->first();

        if ($channelModel === null) {
            return response()->json([
                'status' => 'rejected',
                'message' => 'Channel not found.',
            ], 404);
        }

        $webhookEvent = WebhookEvent::query()->firstOrCreate(
            [
                'workspace_id' => $channelModel->workspace_id,
                'channel_id' => $channelModel->id,
                'event_key' => (string) $validated['event_id'],
            ],
            [
                'event_type' => (string) $validated['event_type'],
                'payload' => $request->json()->all(),
                'headers' => $request->headers->all(),
                'status' => 'received',
                'received_at' => now(),
            ],
        );

        if ($webhookEvent->wasRecentlyCreated) {
            ProcessWebhookEventJob::dispatch($webhookEvent->id);
        }

        return response()->json([
            'status' => 'accepted',
            'webhook_event_id' => $webhookEvent->id,
            'queued' => $webhookEvent->wasRecentlyCreated,
        ], 202);
    }
}
