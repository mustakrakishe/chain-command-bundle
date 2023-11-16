<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\ChainMemberQueueFinishedEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\ChainMemberQueueStartedEvent;
use Mustakrakishe\ChainCommandBundle\Service\LoggingService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles chain master events.
 */
class ChainMemberQueueSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LoggingService $loggingService,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChainMemberQueueStartedEvent::class => [
                ['logChainMemberQueueStarted'],
            ],
            ChainMemberQueueFinishedEvent::class => [
                ['logChainFinished'],
            ],
        ];
    }

    /**
     * Logs an event chain member queue start.
     */
    public function logChainMemberQueueStarted(ChainMemberQueueStartedEvent $event): void
    {
        $this->loggingService->logChainMemberQueueStarted(
            $event->getMasterCommandName()
        );
    }

    /**
     * Logs an event chain finish.
     */
    public function logChainFinished(ChainMemberQueueFinishedEvent $event): void
    {
        $this->loggingService->logChainFinished(
            $event->getMasterCommandName()
        );
    }
}
