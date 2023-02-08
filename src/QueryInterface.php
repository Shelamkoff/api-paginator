<?php

namespace Bermuda\Paginator;

use Bermuda\Arrayable;
use Psr\Http\Message\ServerRequestInterface;

interface QueryInterface extends Arrayable,
    \IteratorAggregate, \Stringable
{
    /**
     * @param string $name
     * @param mixed $value
     * @return QueryInterface
     */
    public function with(string $name, mixed $value): QueryInterface ;

    /**
     * @return string
     */
    public function toString(): string ;

    /**
     * @param string $name
     * @return QueryInterface
     */
    public function withod(string $name): QueryInterface ;

    /**
     * @param string $name
     * @return mixed
     */
    public function __get(string $name): mixed ;

    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool ;

    /**
     * @param ServerRequestInterface $request
     * @return static
     */
    public static function fromRequest(ServerRequestInterface $request): static ;
}
