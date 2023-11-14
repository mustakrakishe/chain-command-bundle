<?php

namespace Mustakrakishe\ChainCommandBundle\Util;

use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

class Formatter implements FormatterInterface
{
    public function format(LogRecord $record): mixed
    {
        return sprintf(
            '[%s] %s',
            $record->datetime->format('Y-m-d H:i:s'),
            $record->message
        ) . PHP_EOL;
    }

    public function formatBatch(array $records): mixed
    {
        return implode(
            array_map([$this, 'format'], $records)
        );
    }
}