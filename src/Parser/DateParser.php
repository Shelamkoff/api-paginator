<?php

namespace App\Query\Parser;

use App\Query\QueryException;
use Bermuda\Clock\Clock;
use Carbon\CarbonInterface;

final class DateParser
{
    public function __construct(
        public readonly string $dateTimeFormat = 'Y-m-d H:i:s'
    ) {
    }

    /**
     * @throws QueryException
     */
    public function __invoke(string $date): CarbonInterface
    {
        if (!Clock::isDate($date)) {
            throw new QueryException('Unable to parse dates string.');
        }

        return Clock::fromFormat($this->dateTimeFormat, $date);
    }
}