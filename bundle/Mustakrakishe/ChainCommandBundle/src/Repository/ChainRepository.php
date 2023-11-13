<?php

namespace Mustakrakishe\ChainCommandBundle\Repository;

/**
 * Provides a chain command repository functional.
 */
class ChainRepository
{
    public function __construct(
        private array $chains,
    ) {
    }

    /**
     * Determines whether the command is registered as an any chain member.
     */
    public function isChainMember(string $commandName): bool
    {
        return !is_null(
            $this->getChainMasterName($commandName)
        );
    }

    /**
     * Gets a registered master command name for passed chain member command name.
     */
    public function getChainMasterName(string $commandName): ?string
    {
        foreach ($this->chains as $chainMasterName => $chainMembers) {
            $chainMemberNames = array_column($chainMembers, 'command');
                
            if (in_array($commandName, $chainMemberNames)) {
                return $chainMasterName;
            }
        }

        return null;
    }

    /**
     * Determines whether the command is registered as an any chain master.
     */
    public function isChainMaster(string $commandName): bool
    {
        return array_key_exists($commandName, $this->chains);
    }

    /**
     * Gets registered master command chained commands.
     */
    public function getChainMembers(string $masterCommandName): array
    {
        return $this->chains[$masterCommandName] ?? [];
    }
}
