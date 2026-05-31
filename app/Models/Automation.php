<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Automation extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['workspace_id', 'name', 'status', 'settings'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function triggers(): HasMany
    {
        return $this->hasMany(AutomationTrigger::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AutomationStep::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AutomationRun::class);
    }
}
