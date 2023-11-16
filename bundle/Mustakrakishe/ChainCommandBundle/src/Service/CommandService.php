<?php

namespace Mustakrakishe\ChainCommandBundle\Service;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides command handling methods.
 */
class CommandService
{
    /**
     * Terminates a command with message output and exit code 2.
     */
    public function terminateAsInvalid(Command $command, string $message): void
    {
        $command->setCode(
            $this->getTerminationCallback(Command::INVALID, $message)
        );
    }

    /**
     * Runs a command with buffered output.
     * 
     * @return int command exit code
     */
    public function runWithBufferedOutput(Command $command, InputInterface $input, string &$outputBuffer): int
    {

        $output = new BufferedOutput();

        $exitCode = $command->run($input, $output);

        $outputBuffer = $output->fetch();

        return $exitCode;
    }

    /**
     * Terminates a command with message output and exit code 0.
     */
    public function terminateAsSuccessful(Command $command, string $message = null): void
    {
        $command->setCode(
            $this->getTerminationCallback(Command::SUCCESS, $message)
        );
    }

    /**
     * Gets a callback for a command execute() method replacing
     * to terminate a command with specified message and exit code.
     */
    private function getTerminationCallback(int $exitCode, string $message = null): callable
    {
        return function (InputInterface $input, OutputInterface $output) use ($exitCode, $message) {
            if (!is_null($message)) {
                $output->writeln($message);
            }

            return $exitCode;
        };
    }
}
