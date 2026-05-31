<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use App\Services\InteractionService;
use App\Services\SocialIntegrationService;
use App\Support\Enums\WebhookEventStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class ProcessWebhookEventJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $webhookEventId) {}

    public function handle(
        SocialIntegrationService $socialIntegrationService,
        InteractionService $interactionService,
    ): void {
        try {
            $webhookEvent = WebhookEvent::query()->with('channel')->findOrFail($this->webhookEventId);

            if ($webhookEvent->status === WebhookEventStatus::Processed->value) {
                return;
            }

            $webhookEvent->update([
                'status' => WebhookEventStatus::Processing->value,
                'error_message' => null,
            ]);

            $normalizedEvent = $socialIntegrationService->normalizeWebhookEvent($webhookEvent);

            $interactionService->ingestNormalizedEvent($webhookEvent, $normalizedEvent);

            $webhookEvent->update([
                'normalized_data' => $normalizedEvent->toArray(),
                'status' => WebhookEventStatus::Processed->value,
                'processed_at' => now(),
            ]);
        } catch (ModelNotFoundException) {
            return;
        } catch (Throwable $throwable) {
            WebhookEvent::query()->whereKey($this->webhookEventId)->update([
                'status' => WebhookEventStatus::Failed->value,
                'error_message' => $throwable->getMessage(),
            ]);

            throw $throwable;
        }
    }
}
