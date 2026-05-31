<?php

use App\Models\ActorIdentity;
use App\Models\Automation;
use App\Models\AutomationRun;
use App\Models\AutomationStep;
use App\Models\Channel;
use App\Models\Interaction;
use App\Models\OutgoingAction;
use App\Models\Tag;
use App\Models\WebhookEvent;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Tests\TestCase;

uses(TestCase::class);

it('defines expected has many and belongs to relationships', function () {
    expect((new Workspace)->channels())->toBeInstanceOf(HasMany::class)
        ->and((new Channel)->workspace())->toBeInstanceOf(BelongsTo::class)
        ->and((new ActorIdentity)->actor())->toBeInstanceOf(BelongsTo::class)
        ->and((new Interaction)->thread())->toBeInstanceOf(BelongsTo::class)
        ->and((new Automation)->triggers())->toBeInstanceOf(HasMany::class)
        ->and((new AutomationStep)->automation())->toBeInstanceOf(BelongsTo::class)
        ->and((new AutomationRun)->outgoingActions())->toBeInstanceOf(HasMany::class)
        ->and((new OutgoingAction)->automationRun())->toBeInstanceOf(BelongsTo::class)
        ->and((new WebhookEvent)->channel())->toBeInstanceOf(BelongsTo::class);
});

it('defines expected tag morph relationships', function () {
    expect((new Tag)->interactions())->toBeInstanceOf(MorphToMany::class)
        ->and((new Tag)->actors())->toBeInstanceOf(MorphToMany::class)
        ->and((new Tag)->threads())->toBeInstanceOf(MorphToMany::class)
        ->and((new Tag)->contentItems())->toBeInstanceOf(MorphToMany::class);
});
