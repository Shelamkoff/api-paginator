<?php

namespace Bermuda\Paginator;

use Bermuda\Arrayable;
use Bermuda\DataObj;
use Bermuda\Paginator\QueryInterface;
use Bermuda\Url\Url;
use Bermuda\Url\UrlSegment;

class Paginator implements Arrayable
{
    private DataObj $query;

    public function __construct(
        private array $results,
        private int $resultsCount,
        private ?Url $url = null,
    ){
        if (!$this->url) $this->url = Url::fromGlobals();
        if ($this->url->query) $query = parse_str($this->url->query);

        $this->query = new DataObj($query ?? []);
    }

    /**
     * @param Url $url
     * @return $this
     */
    public function setUrl(Url $url): self
    {
        $this->url = $url;
        return $this;
    }

    public static function createEmpty(QueryInterface $query = null): self
    {
        return new static([], 0, null, $query);
    }

    /**
     * @param array $results
     * @return $this
     */
    public function setResults(array $results): self
    {
        $this->results = $results;
        return $this;
    }

    /**
     * @return bool
     */
    public function emptyResults(): bool
    {
        return $this->results === [];
    }

    /**
     * @param QueryInterface $query
     * @return $this
     */

    /**
     * @param int $resultsCount
     * @return $this
     */
    public function setResultsCount(int $resultsCount): self
    {
        $this->resultsCount = $resultsCount;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->paginate();
    }

    /**
     * @param array|null $mergeData
     * @return array
     */
    public function paginate(array $mergeData = null): array
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

    private function parseQuery(DataObj $query = null): array
    {
        if ($query === null) $query = $this->query;
        return [($query->limit ?? 10) + 0, ($query->offset ?? 0) + 0];
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
}
