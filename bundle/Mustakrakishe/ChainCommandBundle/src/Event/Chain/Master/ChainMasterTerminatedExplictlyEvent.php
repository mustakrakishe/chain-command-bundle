<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain\Master;

use Symfony\Component\Console\Event\ConsoleTerminateEvent;

/**
 * The event is dispatched each time
 * a console terminates a registered
 * chain master command explictly.
 */
class ChainMasterTerminatedExplictlyEvent
{
    public function __construct(
        private ConsoleTerminateEvent $consoleEvent,
    ) {
    }

    /**
     * Gets causing console command event.
     */
    public function getConsoleEvent(): ConsoleTerminateEvent
    {
        return $this->consoleEvent;
    }
}
