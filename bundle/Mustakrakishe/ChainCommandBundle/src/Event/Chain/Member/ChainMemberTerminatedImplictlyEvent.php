<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain\Member;

/**
 * The event is dispatched each time
 * a chain member command is terminated
 * not within console.
 */
class ChainMemberTerminatedImplictlyEvent
{
    public function __construct(
        private array $member,
        private int $exitStatus,
        private string $outputBuffer,
    ) {
    }

    /**
     * Gets a member data.
     */
    public function getMember(): array
    {
        return $this->member;
    }

    /**
     * Gets an exit status.
     */
    public function getExitStatus(): string
    {
        return $this->exitStatus;
    }

    /**
     * Gets an output buffer.
     */
    public function getOutputBuffer(): string
    {
        return $this->outputBuffer;
    }
}
