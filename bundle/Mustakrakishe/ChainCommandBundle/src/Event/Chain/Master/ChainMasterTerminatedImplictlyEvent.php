<?php

namespace Mustakrakishe\ChainCommandBundle\Event\Chain\Master;

/**
 * The event is dispatched each time
 * a chain master command is terminated
 * not within console.
 */
class ChainMasterTerminatedImplictlyEvent
{
    public function __construct(
        private string $commandName,
        private int $exitStatus,
        private string $outputBuffer,
    ) {
    }

    /**
     * Gets a command name.
     */
    public function getCommandName(): string
    {
        return $this->commandName;
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
