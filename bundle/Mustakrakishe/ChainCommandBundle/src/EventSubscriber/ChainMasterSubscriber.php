<?php

namespace Mustakrakishe\ChainCommandBundle\EventSubscriber;

use Mustakrakishe\ChainCommandBundle\Event\Chain\Master\ChainMasterTerminatedExplictlyEvent;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Handles chain master events.
 */
class ChainMasterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChainRepository $chains,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ChainMasterTerminatedExplictlyEvent::class => [
                ['runChainMemberQueue'],
            ],
        ];
    }

    /**
     * Runs a chain member queue.
     */
    public function runChainMemberQueue(ChainMasterTerminatedExplictlyEvent $event): void
    {
        $consoleEvent = $event->getConsoleEvent();
        $command      = $consoleEvent->getCommand();

        $members = $this->chains->getChainMembers(
            $command->getName()
        );

        $app    = $command->getApplication();
        $output = $consoleEvent->getOutput();

        foreach ($members as $member) {
            $app->find($member['command'])
                ->run(
                    new ArrayInput($member),
                    $output
                );
        }
    }
}
