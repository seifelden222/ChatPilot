<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationTrigger extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['automation_id', 'trigger_type', 'conditions', 'is_active'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }
}
