<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Interaction extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'channel_id',
        'thread_id',
        'actor_id',
        'actor_identity_id',
        'content_item_id',
        'interaction_key',
        'direction',
        'type',
        'status',
        'body',
        'occurred_at',
        'metadata',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'occurred_at' => 'datetime',
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

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }

    public function actorIdentity(): BelongsTo
    {
        return $this->belongsTo(ActorIdentity::class);
    }

    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }

    public function automationRuns(): HasMany
    {
        return $this->hasMany(AutomationRun::class);
    }
}
