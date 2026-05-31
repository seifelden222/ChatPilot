<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = ['workspace_id', 'name', 'slug', 'color'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function interactions(): MorphToMany
    {
        return $this->morphedByMany(Interaction::class, 'taggable');
    }

    public function actors(): MorphToMany
    {
        return $this->morphedByMany(Actor::class, 'taggable');
    }

    public function threads(): MorphToMany
    {
        return $this->morphedByMany(Thread::class, 'taggable');
    }

    public function contentItems(): MorphToMany
    {
        return $this->morphedByMany(ContentItem::class, 'taggable');
    }
}
