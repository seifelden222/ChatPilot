<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActorIdentity extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['workspace_id', 'channel_id', 'actor_id', 'identity_key', 'identity_data'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['identity_data' => 'array'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(Actor::class);
    }
}
