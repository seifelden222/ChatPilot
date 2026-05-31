<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actor extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['workspace_id', 'display_name', 'actor_type', 'metadata'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['metadata' => 'array'];
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(ActorIdentity::class);
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }
}
