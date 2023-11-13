<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain\Member;

use Symfony\Component\Console\Event\ConsoleCommandEvent;

/**
 * The event is dispatched each time
 * a console runs a registered
 * chain member command explictly.
 */
class ChainMemberExecutedExplictlyEvent
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