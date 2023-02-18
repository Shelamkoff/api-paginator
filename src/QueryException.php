<?php

namespace Bermuda\Paginator\Query;

use Bermuda\HTTP\BadRequestException

class QueryException extends \BadRequestException
{
    public function __construct(string $reasonPhrase = null)
    {
        parent::__construct($reasonPhrase);
        $this->code = 422;
    }
}
