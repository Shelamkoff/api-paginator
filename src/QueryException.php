<?php

namespace Bermuda\Paginator\Query;

use Bermuda\HTTP\Exception\BadRequestException;

class ParserException extends BadRequestException
{
    public function __construct(string $reasonPhrase = null)
    {
        parent::__construct($reasonPhrase);
        $this->code = 422;
    }
}
