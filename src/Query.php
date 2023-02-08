<?php

namespace Bermuda\Pagination;

use Cycle\ORM\Select;
use Cycle\ORM\SchemaInterface;

class Query implements QueryInterface
{
    public function __construct(
        public readonly string $role,
        protected array $data = [],
    ) {
    }

    public function __toString(): string
    {
        return http_build_query($this->data);
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function getIterator(): \Generator
    {
        foreach ($this->data as $prop => $value) yield $prop => $value;
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->data);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->offsetExists($offset) ?
            $this->data[$offset] : $this->data[$offset] = [];
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->data[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->data[$offset]);
    }
}
