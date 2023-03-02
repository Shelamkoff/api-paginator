<?php

namespace Bermuda\Stdlib\Paginator;

final class Paginator implements Arrayable
{
    private DataObj $query;
    public readonly Url $url;

    public function __construct(
        public readonly array $results,
        public readonly int $resultsCount,
        ?Url $url = null,
    ) {
        $this->url = $url ? $url : Url::fromGlobals();
        if ($this->url->query) parse_str($this->url->query, $query);

        $this->query = new DataObj($query ?? []);
    }

    public static function createEmpty(): self
    {
        return new static([], 0);
    }

    /**
     * @return bool
     */
    public function emptyResults(): bool
    {
        return $this->results === [];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if ($this->results == []) {
            return [];
        }

        $data = [
            'count'   => $this->resultsCount,
            'prev'    => $this->getPrevUrl(),
            'next'    => $this->getNextUrl(),
            'range'   => $this->getRange(),
            'results' => $this->results
        ];

        if ($mergeData != null) {
            return array_merge($mergeData, $data);
        }

        return $data;
    }

    /**
     * @return string|null
     */
    public function getNextUrl():? string
    {
        $query = clone $this->query;
        list($limit, $offset) = $this->parseQuery();
        if ($this->resultsCount > ($offset = $limit + $offset)) {
            $query->set('offset', $offset);
            return $this->url->withQuery($query->toArray())->toString();
        }

        return null;
    }

    /**
     * @return int[]
     */
    public function getRange(): array
    {
        list(, $offset) = $this->parseQuery();
        if ($offset == 0) return [1,  count($this->results)];

        return [$offset + 1, $offset + count($this->results)];
    }

    /**
     * @return string|null
     */
    public function getPrevUrl():? string
    {
        $query = clone $this->query;
        list($limit, $offset) = $this->parseQuery($query);

        if ($offset != 0) {
            if (($diff = $offset - $limit) > 0) $query = $query->set('offset', $diff);
            elseif ($diff == 0) $query->offsetUnset('offset');

            $this->query->set('offset', $offset);
            return $this->url->withQuery($this->query->toArray())->toString();
        }

        return null;
    }
    
    private function parseQuery(DataObj $query = null): array
    {
        if ($query === null) $query = $this->query;
        return [($query->limit ?? 10) + 0, ($query->offset ?? 0) + 0];
    }
}
