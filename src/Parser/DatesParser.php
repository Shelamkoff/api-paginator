<?php

namespace Bermuda\Paginator\Parser;

use Bermuda\Clock\Clock;

final class DatesParser
{
    public function __construct(
        public readonly string $delimiter = ',',
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @throws QueryException
     */
    public function __invoke(string $dates): array
    {
        $arr = [];
        foreach (explode($this->delimiter, $dates) as $date) {
            if (!Clock::isDate($date)) {
                throw new QueryException('Unable to parse dates string.');
            }

            $arr[] = Clock::fromFormat($this->dateTimeFormat, $date);
        }

        return $arr;
    }
}
