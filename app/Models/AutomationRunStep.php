<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRunStep extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'automation_run_id',
        'automation_step_id',
        'step_order',
        'status',
        'result',
        'executed_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'result' => 'array',
            'executed_at' => 'datetime',
        ];
    }

    public function automationRun(): BelongsTo
    {
        return $this->belongsTo(AutomationRun::class);
    }

    public function automationStep(): BelongsTo
    {
        return $this->belongsTo(AutomationStep::class);
    }
}
