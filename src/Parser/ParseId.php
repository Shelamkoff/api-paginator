<?php

namespace Paginator\Query\Parser;

final class ParseId
{
    public function __construct(public readonly string $delimiter = ',')
    {
    }

    /**
     * @throws QueryException
     */
    public function __invoke(string $ids): array
    {
        $arr = [];
        foreach (explode($this->delimiter, $ids) as $id) {
            $arr[] = $id;
            if (!is_numeric($id)) throw new QueryException('Id is not numeric');
        }

        return $arr;
    }
}
