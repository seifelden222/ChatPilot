<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\Interaction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class AutomationMatcher
{
    public function match(Interaction $interaction): Collection
    {
        $body = Str::lower((string) $interaction->body);

        if ($body === '') {
            return new Collection;
        }

        return Automation::query()
            ->where('workspace_id', $interaction->workspace_id)
            ->where('status', 'active')
            ->whereHas('triggers', function ($query): void {
                $query->where('trigger_type', 'keyword')
                    ->where('is_active', true);
            })
            ->with(['triggers' => function ($query): void {
                $query->where('trigger_type', 'keyword')
                    ->where('is_active', true);
            }])
            ->get()
            ->filter(function (Automation $automation) use ($body): bool {
                foreach ($automation->triggers as $trigger) {
                    $conditions = $trigger->conditions ?? [];
                    $keywords = [];

                    if (array_key_exists('keyword', $conditions)) {
                        $keywords[] = $conditions['keyword'];
                    }

                    if (array_key_exists('keywords', $conditions) && is_array($conditions['keywords'])) {
                        $keywords = array_merge($keywords, $conditions['keywords']);
                    }

                    foreach ($keywords as $keyword) {
                        $keyword = Str::lower(trim((string) $keyword));

                        if ($keyword !== '' && str_contains($body, $keyword)) {
                            return true;
                        }
                    }
                }

                return false;
            })
            ->values();
    }
}
