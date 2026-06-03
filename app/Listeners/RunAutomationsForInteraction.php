<?php

namespace App\Listeners;

use App\Events\InteractionCreated;
use App\Models\Interaction;
use App\Services\AutomationExecutor;
use App\Services\AutomationMatcher;

class RunAutomationsForInteraction
{
    public function __construct(
        private AutomationMatcher $automationMatcher,
        private AutomationExecutor $automationExecutor,
    ) {}

    public function handle(InteractionCreated $event): void
    {
        $interaction = Interaction::query()->find($event->interactionId);

        if ($interaction === null) {
            return;
        }

        $this->automationMatcher->match($interaction)->each(function ($automation) use ($interaction): void {
            $this->automationExecutor->execute($automation, $interaction);
        });
    }
}
