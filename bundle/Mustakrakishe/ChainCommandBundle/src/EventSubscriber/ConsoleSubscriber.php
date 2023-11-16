<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterExecutedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterTerminatedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Event\Chain\Member\ChainMemberExecutedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
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
            ],
            ConsoleTerminateEvent::class => [
                ['dispatchRegisteredCommandTerminatedExplictlyEvent'],
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

            return;
        }

        if ($this->chains->isChainMaster($commandName)) {
            $this->dispatcher->dispatch(
                new ChainMasterExecutedExplictlyEvent($event)
            );
        }
    }

    /**
     * Dispatches a relevant event
     * if an explictly terminated command
     * is registered in chains.
     */
    public function dispatchRegisteredCommandTerminatedExplictlyEvent(ConsoleTerminateEvent $event): void
    {
        $commandName = $event->getCommand()->getName();

        if ($this->chains->isChainMember($commandName)) {
            return;
        }

        if ($this->chains->isChainMaster($commandName)) {
            $this->dispatcher->dispatch(
                new ChainMasterTerminatedExplictlyEvent($event)
            );
        }
    }
}
