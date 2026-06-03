<?php

namespace App\Jobs;

use App\Models\OutgoingAction;
use App\Services\SocialIntegrationService;
use App\Support\Enums\OutgoingActionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExecuteOutgoingActionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $outgoingActionId) {}

    public function handle(SocialIntegrationService $socialIntegrationService): void
    {
        DB::transaction(function () use ($socialIntegrationService): void {
            $outgoingAction = OutgoingAction::query()
                ->with(['interaction', 'channel'])
                ->find($this->outgoingActionId);

            if ($outgoingAction === null) {
                throw new ModelNotFoundException;
            }

            if (in_array($outgoingAction->status, [
                OutgoingActionStatus::Succeeded->value,
                OutgoingActionStatus::Failed->value,
            ], true)) {
                return;
            }

            if (! in_array($outgoingAction->status, [
                OutgoingActionStatus::Pending->value,
                OutgoingActionStatus::RetryPending->value,
            ], true)) {
                return;
            }

            $outgoingAction->update([
                'status' => OutgoingActionStatus::InProgress->value,
            ]);

            try {
                $response = $socialIntegrationService->executeOutgoingAction($outgoingAction);

                $outgoingAction->update([
                    'status' => OutgoingActionStatus::Succeeded->value,
                    'provider_response' => $response,
                    'executed_at' => CarbonImmutable::now(),
                    'failed_at' => null,
                    'failure_reason' => null,
                ]);
            } catch (Throwable $throwable) {
                $errorResponse = [
                    'provider' => 'fake',
                    'status' => 'failed',
                    'error' => $throwable->getMessage(),
                ];

                $outgoingAction->update([
                    'status' => OutgoingActionStatus::Failed->value,
                    'provider_response' => $errorResponse,
                    'failed_at' => CarbonImmutable::now(),
                    'failure_reason' => $throwable->getMessage(),
                ]);

                return;
            }
        });
    }
}
