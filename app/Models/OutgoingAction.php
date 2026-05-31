<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutgoingAction extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'channel_id',
        'automation_run_id',
        'interaction_id',
        'action_type',
        'payload',
        'status',
        'provider_response',
        'attempts',
        'scheduled_at',
        'executed_at',
        'failed_at',
        'failure_reason',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'provider_response' => 'array',
            'scheduled_at' => 'datetime',
            'executed_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function automationRun(): BelongsTo
    {
        return $this->belongsTo(AutomationRun::class);
    }

    public function interaction(): BelongsTo
    {
        return $this->belongsTo(Interaction::class);
    }
}
