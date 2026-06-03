<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workspace extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['name', 'slug', 'settings'];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function actors(): HasMany
    {
        return $this->hasMany(Actor::class);
    }

    public function automations(): HasMany
    {
        return $this->hasMany(Automation::class);
    }

    public function tags(): HasMany
    {
        return $this->hasMany(Tag::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
