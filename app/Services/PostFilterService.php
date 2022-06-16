<?php

namespace App\Services;


use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
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

    public function byGroup(?Collection $group_ids = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null === $group_ids) {
            return $this;
        }

        $this->query = $this->query
            ->whereHas('account.person.groups', function (Builder $query) use ($group_ids) {
            $query->whereIn('groups.id', $group_ids);
        });

        return $this;
    }

    public function byNetwork(?Collection $networks = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null === $networks) {
            return $this;
        }

        $this->query = $this->query
            ->whereHas('account', function (Builder $query) use ($networks) {
                $query->whereIn('network', $networks);
            });

        return $this;
    }

    public function byContent(?string $text = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null === $text) {
            return $this;
        }

        $sanitized = trim(htmlentities($text, ENT_QUOTES, 'UTF-8', false));
        $this->query = $this->query->where('content', 'LIKE', "%$sanitized%");

        return $this;
    }

    public function byDates(?string $from = null, ?string $to = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null !== $from) {
            try {
                $this->query = $this->query->whereDate('posted_at', '>=', Carbon::parse($from));
            } catch (InvalidFormatException $exception) {
                //
            }
        }

        if (null !== $to) {
            try {
                $this->query = $this->query->whereDate('posted_at', '<=', Carbon::parse($to));
            } catch (InvalidFormatException $exception) {
                //
            }
        }

        return $this;
    }
}
