<?php

namespace App\Services;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class PostFilterService
{
    protected ?Builder $query = null;

    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    public function byGroup(Collection $group_ids): self
    {
        if (null === $this->query) {
            return $this;
        }

        $this->query = $this->query
            ->whereHas('account.person.groups', function (Builder $query) use ($group_ids) {
            $query->whereIn('groups.id', $group_ids);
        });

        return $this;
    }

    public function byContent(string $text): self
    {
        if (null === $this->query) {
            return $this;
        }

        $sanitized = trim(htmlentities($text, ENT_QUOTES, 'UTF-8', false));
        $this->query = $this->query->where('content', 'LIKE', "%$sanitized%");

        return $this;
    }
}
