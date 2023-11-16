<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\Member\ChainMemberExecutedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Member\ChainMemberTerminatedImplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Mustakrakishe\ChainCommandBundle\Service\CommandService;
use Mustakrakishe\ChainCommandBundle\Service\LoggingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles chain member event.
 */
class ChainMemberSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChainRepository $chains,
        private CommandService $commandService,
        private LoggingService $loggingService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChainMemberExecutedExplictlyEvent::class => [
                ['terminateCommandAsInvalid'],
            ],
            ChainMemberTerminatedImplictlyEvent::class => [
                ['displayOutputBuffer'],
                ['logChainMemberTerminated'],
            ],
        ];
    }

    /**
     * Terminates a console executed chain member command with exit code 2.
     */
    public function terminateCommandAsInvalid(ChainMemberExecutedExplictlyEvent $event): void
    {
        $command = $event->getConsoleEvent()->getCommand();

        $message = $this->createInvalidTerminationMessage(
            $command->getName()
        );

        $this->commandService->terminateAsInvalid($command, $message);
    }

    /**
     * Creates a message for a member command termination as invalid.
     */
    private function createInvalidTerminationMessage(string $memberCommandName): string
    {
        return sprintf(
            'Error: %s command is a member of %s command chain and cannot be executed on its own.',
            $memberCommandName,
            $this->chains->getChainMasterName($memberCommandName)
        );
    }

    /**
     * Displays a command output buffer in console.
     */
    public function displayOutputBuffer(ChainMemberTerminatedImplictlyEvent $event): void
    {
        echo $event->getOutputBuffer();
    }

    /**
     * Logs a chain member implict execution finish.
     */
    public function logChainMemberTerminated(ChainMemberTerminatedImplictlyEvent $event): void
    {
        $this->loggingService->logOutputBuffer(
            $event->getOutputBuffer()
        );
    }
}
