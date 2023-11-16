<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain;

/**
 * The event is dispatched each time
 * a chain member queue finishes
 * the running of its members.
 */
class ChainMemberQueueFinishedEvent
{
    public function __construct(
        private string $masterCommandName,
        private array  $members,
    ) {
    }

    /**
     * Gets maaster command name.
     */
    public function getMasterCommandName(): string
    {
        return $this->masterCommandName;
    }

    /**
     * Gets members.
     */
    public function getMembers(): array
    {
        return $this->members;
    }
}
