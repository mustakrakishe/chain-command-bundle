<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain\Master;

use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * The event is dispatched each time
 * a console runs a registered
 * chain aster command explictly.
 */
class ChainMasterExecutedExplictlyEvent
{
    public function __construct(
        private ConsoleCommandEvent $consoleEvent,
    ) {
    }

    /**
     * Gets causing console command event.
     */
    public function getConsoleEvent(): ConsoleCommandEvent
    {
        return $this->consoleEvent;
    }
}