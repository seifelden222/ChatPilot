<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationStep extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['automation_id', 'step_order', 'step_type', 'configuration', 'is_active'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'configuration' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }

    public function runSteps(): HasMany
    {
        return $this->hasMany(AutomationRunStep::class);
    }
}
