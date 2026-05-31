<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Thread extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'channel_id',
        'thread_key',
        'status',
        'last_interaction_at',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_interaction_at' => 'datetime',
            'metadata' => 'array',
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

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
