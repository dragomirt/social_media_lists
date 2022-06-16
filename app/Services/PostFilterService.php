<?php

namespace App\Services;


use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PostFilterService
{
    protected ?Builder $query = null;

    /**
     * Based on the Builder pattern, well suited for chaining.
     *
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

    public function getQuery(): ?Builder
    {
        return $this->query;
    }

    /**
     * Filters by group using ids.
     *
     * @param Collection|null $group_ids
     * @return $this
     */
    public function byGroup(?Collection $group_ids = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null === $group_ids || $group_ids->isEmpty()) {
            return $this;
        }

        $this->query = $this->query
            ->whereHas('account.person.groups', function (Builder $query) use ($group_ids) {
            $query->whereIn('groups.id', $group_ids);
        });

        return $this;
    }

    /**
     * Filters by network.
     *
     * @param Collection|null $networks
     * @return $this
     */
    public function byNetwork(?Collection $networks = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null === $networks || $networks->isEmpty()) {
            return $this;
        }

        $this->query = $this->query
            ->whereHas('account', function (Builder $query) use ($networks) {
                $query->whereIn('network', $networks);
            });

        return $this;
    }

    /**
     * Filters content using the like function. Text is sanitized.
     *
     * @param string|null $text
     * @return $this
     */
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

    /**
     * Filters posts by form and to range.
     *
     * @param string|null $from
     * @param string|null $to
     * @return $this
     */
    public function byDates(?string $from = null, ?string $to = null): self
    {
        if (null === $this->query) {
            return $this;
        }

        if (null !== $from) {
            try {
                $from_date = Carbon::parse($from);
                $this->query = $this->query->whereDate('posted_at', '>=', $from_date);
            } catch (InvalidFormatException $exception) {
                //
            }
        }

        if (null !== $to) {
            try {
                $to_date = Carbon::parse($to);
                $this->query = $this->query->whereDate('posted_at', '<=', $to_date);
            } catch (InvalidFormatException $exception) {
                //
            }
        }

        return $this;
    }
}
