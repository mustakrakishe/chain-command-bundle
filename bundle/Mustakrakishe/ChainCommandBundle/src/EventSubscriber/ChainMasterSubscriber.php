<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\ChainMemberQueueFinishedEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\ChainMemberQueueStartedEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterExecutedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterTerminatedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterTerminatedImplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Member\ChainMemberTerminatedImplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Mustakrakishe\ChainCommandBundle\Service\CommandService;
use Mustakrakishe\ChainCommandBundle\Service\LoggingService;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Handles chain master events.
 */
class ChainMasterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChainRepository $chains,
        private LoggingService $loggingService,
        private EventDispatcherInterface $dispatcher,
        private CommandService $commandService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChainMasterExecutedExplictlyEvent::class => [
                ['logRegisteredChain'],
                ['logChainMasterExecuted'],
                ['rerunWithBufferedOutput'],
            ],
            ChainMasterTerminatedImplictlyEvent::class => [
                ['displayOutputBuffer'],
                ['logChainMasterTerminated'],
            ],
            ChainMasterTerminatedExplictlyEvent::class => [
                ['runChainMemberQueue'],
            ],
        ];
    }

    /**
     * Logs registered chain.
     */
    public function logRegisteredChain(ChainMasterExecutedExplictlyEvent $event): void
    {
        $masterCommandName = $event->getConsoleEvent()->getCommand()->getName();

        $memberCommandNames = array_column(
            $this->chains->getChainMembers($masterCommandName),
            'command'
        );

        $this->loggingService->logRegisteredChain($masterCommandName, $memberCommandNames);
    }

    /**
     * Logs a chain master start.
     */
    public function logChainMasterExecuted(ChainMasterExecutedExplictlyEvent $event): void
    {
        $this->loggingService->logChainMasterExecuted(
            $event->getConsoleEvent()->getCommand()->getName()
        );
    }

    public function rerunWithBufferedOutput(ChainMasterExecutedExplictlyEvent $event): void
    {
        $consoleEvent = $event->getConsoleEvent();
        $command      = $consoleEvent->getCommand();

        $outputBuffer = '';

        $exitCode = $this->commandService->runWithBufferedOutput(
            $command,
            $consoleEvent->getInput(),
            $outputBuffer
        );

        $this->dispatcher->dispatch(
            new ChainMasterTerminatedImplictlyEvent(
                $command->getName(),
                $exitCode,
                $outputBuffer
            )
        );

        $this->commandService->terminateAsSuccessful($command);
    }

    /**
     * Displays a command output buffer in console.
     */
    public function displayOutputBuffer(ChainMasterTerminatedImplictlyEvent $event): void
    {
        echo $event->getOutputBuffer();
    }

    /**
     * Logs a chain master implict execution finish.
     */
    public function logChainMasterTerminated(ChainMasterTerminatedImplictlyEvent $event): void
    {
        $this->loggingService->logOutputBuffer(
            $event->getOutputBuffer()
        );
    }

    /**
     * Runs a chain member queue.
     */
    public function runChainMemberQueue(ChainMasterTerminatedExplictlyEvent $event): void
    {
        $consoleEvent      = $event->getConsoleEvent();
        $masterCommand     = $consoleEvent->getCommand();
        $masterCommandName = $masterCommand->getName();

        $members = $this->chains->getChainMembers($masterCommandName);

        $app    = $masterCommand->getApplication();
        $output = $consoleEvent->getOutput();

        $this->dispatcher->dispatch(
            new ChainMemberQueueStartedEvent($masterCommandName, $members)
        );

        foreach ($members as $member) {
            $outputBuffer = '';

            $exitCode = $this->commandService->runWithBufferedOutput(
                $app->find($member['command']),
                new ArrayInput($member),
                $outputBuffer
            );

            $this->dispatcher->dispatch(
                new ChainMemberTerminatedImplictlyEvent(
                    $member,
                    $exitCode,
                    $outputBuffer
                )
            );
        }

        $this->dispatcher->dispatch(
            new ChainMemberQueueFinishedEvent($masterCommandName, $members)
        );
    }
}
