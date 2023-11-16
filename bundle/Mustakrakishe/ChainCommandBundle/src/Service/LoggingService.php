<?php

namespace Mustakrakishe\ChainCommandBundle\Service;

use Psr\Log\LoggerInterface;

/**
 * Provides chain command logging methods.
 */
class LoggingService
{
    public function __construct(
        private LoggerInterface $logger,
    ) {  
    }

    /**
     * Logs registered chain info.
     */
    public function logRegisteredChain(string $masterCommandName, array $memberCommandNames): void
    {
        $this->logRegisteredChainMaster($masterCommandName);

        foreach ($memberCommandNames as $memberCommandName) {
            $this->logRegisteredChainMember($masterCommandName, $memberCommandName);
        }
    }

    /**
     * Logs registered chain master command info.
     */
    public function logRegisteredChainMaster(string $masterCommandName): void
    {
        $this->logger->info(
            '{master_command} is a master command of a command chain that has registered member commands',
            [
                'master_command' => $masterCommandName,
            ]
        );
    }

    /**
     * Logs registered chain member command info.
     */
    public function logRegisteredChainMember(string $masterCommandName, string $memberCommandName): void
    {
        $this->logger->info(
            '{member_command} registered as a member of {master_command} command chain',
            [
                'member_command' => $memberCommandName,
                'master_command' => $masterCommandName,
            ]
        );
    }

    /**
     * Logs a chain master command start.
     */
    public function logChainMasterExecuted(string $masterCommandName): void
    {
        $this->logger->info(
            'Executing {master_command} command itself first:',
            [
                'master_command' => $masterCommandName,
            ]
        );
    }

    /**
     * Logs a chain member queue start.
     */
    public function logChainMemberQueueStarted(string $masterCommandName): void
    {
        $this->logger->info(
            'Executing {master_command} chain members:',
            [
                'master_command' => $masterCommandName,
            ]
        );
    }

    /**
     * Logs a chain finish.
     */
    public function logChainFinished(string $masterCommandName): void
    {
        $this->logger->info(
            'Execution of {master_command} chain completed.',
            [
                'master_command' => $masterCommandName,
            ]
        );
    }
}
