<?php

namespace Bermuda\Paginator;

use Bermuda\Url\Url;
use Bermuda\Url\UrlSegment;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @property-read int $limit
 * @property-read int $offset
 */
class Query implements QueryInterface
{
    public const limit = 'limit';
    public const offset = 'offset';

    public function __construct(
        public readonly Url $url,
        protected array $queryParams = [],
    ) {
    }

    public static function fromGlobals(): static
    {
        return new static(Url::fromGlobals()->withod('query'), $_GET);
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toArray(): array
    {
        return $this->queryParams;
    }

    public function getIterator(): \Generator
    {
        foreach ($this->queryParams as $prop => $value) yield $prop => $value;
    }

    public function with(string $name, mixed $value): QueryInterface
    {
        $copy = clone $this;
        $copy->queryParams[$name] = $value;

        return $copy;
    }

    public function withod(string $name): QueryInterface
    {
        $copy = clone $this;
        unset($copy->queryParams[$name]);

        return $copy;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed
    {
        return $this->queryParams[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return array_key_exists($name, $this->queryParams);
    }

    public function toString(): string
    {
        return $this->url->withQuery($this->queryParams)->toString();
    }

    public static function fromRequest(ServerRequestInterface $request): static
    {
        $query = [];
        $params = static::mergeDefaults($request->getQueryParams());

        foreach (static::getParseCallbacks() as $name => $callback) {
            if (isset($params[$name])) $query[$name] = $callback($params[$name]);
        }

        return new static(new Url([
            UrlSegment::host => $request->getUri()->getHost(),
            UrlSegment::scheme => $request->getUri()->getScheme(),
            UrlSegment::path => $request->getUri()->getPath(),
        ]), $query);
    }

    protected static function mergeDefaults(array $queryParams): array
    {
        return array_merge([self::limit => 10, self::offset => 0], $queryParams);
    }

    protected static function getParseCallbacks(): array
    {
        return [
            static::limit => static function(string $limit): int {
                if (!is_numeric($limit)) {
                    throw new QueryException('Query param: ['.static::limit.'] must be a numeric.');
                }
                return $limit;
            },
            static::offset => static function(string $offset): int {
                if (!is_numeric($offset)) {
                    throw new QueryException('Query param: ['.static::offset.'] must be a numeric.');
                }
                return $offset;
            },
        ];
    }
}
