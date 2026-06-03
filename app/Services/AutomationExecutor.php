<?php

namespace App\Services;

use App\Events\OutgoingActionCreated;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationRunStep;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Support\Enums\AutomationRunStatus;
use App\Support\Enums\OutgoingActionStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class AutomationExecutor
{
    public function execute(Automation $automation, Interaction $interaction): AutomationRun
    {
        return DB::transaction(function () use ($automation, $interaction): AutomationRun {
            $automation->loadMissing('steps');

            $run = AutomationRun::query()->create([
                'workspace_id' => $automation->workspace_id,
                'automation_id' => $automation->id,
                'interaction_id' => $interaction->id,
                'status' => AutomationRunStatus::Running->value,
                'started_at' => CarbonImmutable::now(),
                'summary' => [],
            ]);

            foreach ($automation->steps()->where('is_active', true)->orderBy('step_order')->get() as $step) {
                $runStep = AutomationRunStep::query()->create([
                    'automation_run_id' => $run->id,
                    'automation_step_id' => $step->id,
                    'step_order' => $step->step_order,
                    'status' => 'completed',
                    'result' => [
                        'step_type' => $step->step_type,
                    ],
                    'executed_at' => CarbonImmutable::now(),
                ]);

                if (in_array($step->step_type, ['send_message', 'send_reply'], true)) {
                    $outgoingAction = OutgoingAction::query()->create([
                        'workspace_id' => $automation->workspace_id,
                        'channel_id' => $interaction->channel_id,
                        'automation_run_id' => $run->id,
                        'interaction_id' => $interaction->id,
                        'action_type' => $step->step_type,
                        'payload' => $step->configuration ?? [],
                        'status' => OutgoingActionStatus::Pending->value,
                    ]);

                    OutgoingActionCreated::dispatch(
                        outgoingActionId: $outgoingAction->id,
                        workspaceId: $outgoingAction->workspace_id,
                        channelId: $outgoingAction->channel_id,
                        automationRunId: $outgoingAction->automation_run_id,
                        interactionId: $outgoingAction->interaction_id,
                    );

                    $runStep->update([
                        'result' => [
                            'step_type' => $step->step_type,
                            'outgoing_action_id' => $outgoingAction->id,
                        ],
                    ]);
                }
            }

            $run->update([
                'status' => AutomationRunStatus::Completed->value,
                'completed_at' => CarbonImmutable::now(),
            ]);

            return $run->refresh();
        });
    }
}
