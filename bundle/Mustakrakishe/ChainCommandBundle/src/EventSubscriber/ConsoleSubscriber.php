<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\Member\ChainMemberExecutedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Handles console events
 * to manage chain command events.
 */
class ConsoleSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChainRepository $chains,
        private EventDispatcherInterface $dispatcher,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleCommandEvent::class => [
                ['dispatchRegisteredCommandExecutedExplictlyEvent'],
                ['dispatchRegisteredCommandExecutedEvent'],
            ],
        ];
    }

    /**
     * Dispatches a relevant event
     * if an explictly executed command
     * is registered in chains.
     */
    public function dispatchRegisteredCommandExecutedExplictlyEvent(ConsoleCommandEvent $event): void
    {
        $commandName = $event->getCommand()->getName();

        if ($this->chains->isChainMember($commandName)) {
            $this->dispatcher->dispatch(
                new ChainMemberExecutedExplictlyEvent($event)
            );
        }
    }
}
