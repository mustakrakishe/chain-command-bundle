<?php

namespace Mustakrakishe\ChainCommandBundle\Test;

use Exception;
use Mustakrakishe\ChainCommandBundle\Repository\ChainRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\ApplicationTester;

class FunctionalTest extends KernelTestCase
{
    /**
     * @dataProvider unregisteredCommandsProvider
     */
    public function testPassUnregisteredCommands(int $expectedExitCode, string $expectedOutput, string $commandToRun, array $appCommands, array $chains): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();
        $container->set(
            ChainRepository::class,
            new ChainRepository($chains)
        );

        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->addCommands($appCommands);

        $appTester = new ApplicationTester($app);
        $appTester->run(['command' => $commandToRun]);

        $this->assertEquals($expectedExitCode, $appTester->getStatusCode(), 'Exit code is correct.');
        $this->assertEquals($expectedOutput, $appTester->getDisplay(), 'Output is correct');
    }

    /**
     * @dataProvider correctChainsProvider
     */
    public function testCorrectResultOnCorrectChainsExecution(int $expectedExitCode, string $expectedOutput, string $commandToRun, array $appCommands, array $chains): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();
        $container->set(
            ChainRepository::class,
            new ChainRepository($chains)
        );

        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->addCommands($appCommands);

        $appTester = new ApplicationTester($app);

        ob_start();
        $appTester->run(['command' => $commandToRun]);
        $outputBuffer = ob_get_clean();

        $this->assertEquals($expectedExitCode, $appTester->getStatusCode(), 'Exit code is correct.');
        $this->assertEquals($expectedOutput, $outputBuffer, 'Output is correct');
    }

    /**
     * @dataProvider memberCommandsProvider
     */
    public function testCorrectResultOnMemberExecution(int $expectedExitCode, string $expectedOutput, string $commandToRun, array $appCommands, array $chains): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();
        $container->set(
            ChainRepository::class,
            new ChainRepository($chains)
        );

        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->addCommands($appCommands);

        $appTester = new ApplicationTester($app);
        $appTester->run(['command' => $commandToRun]);

        $this->assertEquals($expectedExitCode, $appTester->getStatusCode(), 'Exit code is correct.');
        $this->assertEquals($expectedOutput, $appTester->getDisplay(), 'Output is correct');
    }

    /**
     * @dataProvider chainWithExceptionProvider
     */
    public function testCorrectResultOnChainWithExceptionExecution(int $expectedExitCode, string $expectedOutput, string $commandToRun, array $appCommands, array $chains): void
    {
        $kernel = self::bootKernel();

        $container = self::getContainer();
        $container->set(
            ChainRepository::class,
            new ChainRepository($chains)
        );

        $app = new Application($kernel);
        $app->setAutoExit(false);
        $app->addCommands($appCommands);

        $appTester = new ApplicationTester($app);

        ob_start();
        $appTester->run(['command' => $commandToRun]);
        $outputBuffer = ob_get_clean();

        $this->assertEquals($expectedExitCode, $appTester->getStatusCode(), 'Exit code is correct.');
        $this->assertEquals($expectedOutput, $outputBuffer, 'Output is correct');
    }

    private function createTestCommand(string $name, int $exitCode, string $message = null): Command
    {
        $command = new Command($name);

        $command->setCode(function (InputInterface $input, OutputInterface $output) use ($exitCode, $message) {
            if (Command::FAILURE === $exitCode) {
                throw new Exception($message, $exitCode);
            }

            $output->writeln($message);

            return $exitCode;
        });

        return $command;
    }

    public function unregisteredCommandsProvider(): array
    {
        return [
            [
                'expected_exit_code' => Command::SUCCESS,
                'expected_output'    => 'Unregistered command success!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_unregistered_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_unregistered_comand',
                        Command::SUCCESS,
                        'Unregistered command success!'
                    ),
                ],
                'chains'             => [],
            ],
        ];
    }

    public function correctChainsProvider(): array
    {
        return [
            'run_master_without_members' => [
                'expected_exit_code' => Command::SUCCESS,
                'expected_output'    => 'Master success!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => []
                ],
            ],
            'run_master_with_one_member' => [
                'expected_exit_code' => Command::SUCCESS,
                'expected_output'    => 'Master success!'.PHP_EOL.'Member success!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand',
                        Command::SUCCESS,
                        'Member success!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand'],
                    ],
                ],
            ],
            'run_master_with_two_members' => [
                'expected_exit_code' => Command::SUCCESS,
                'expected_output'    => 'Master success!'.PHP_EOL.'Member success 1!'.PHP_EOL.'Member success 2!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand_1',
                        Command::SUCCESS,
                        'Member success 1!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand_2',
                        Command::SUCCESS,
                        'Member success 2!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand_1'],
                        ['command' => 'mustakrakishe_chain_command:test_member_comand_2'],
                    ],
                ],
            ],
            'run_master_with_member_that_is_also_master' => [
                'expected_exit_code' => Command::SUCCESS,
                'expected_output'    => 'Master success!'.PHP_EOL.'Member success 1!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand_1',
                        Command::SUCCESS,
                        'Member success 1!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand_2',
                        Command::SUCCESS,
                        'Member success 2!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand_1'],
                    ],
                    'mustakrakishe_chain_command:test_member_comand_1' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand_2'],
                    ],
                ],
            ],
        ];
    }

    public function memberCommandsProvider(): array
    {
        return [
            'run_member' => [
                'expected_exit_code' => Command::INVALID,
                'expected_error'     => 'Error: mustakrakishe_chain_command:test_member_comand command'
                                     . ' is a member of mustakrakishe_chain_command:test_master_comand command chain'
                                     . ' and cannot be executed on its own.'
                                     . PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_member_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand',
                        Command::SUCCESS,
                        'Member success 1!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand'],
                    ],
                ],
            ],
            'run_member_that_is_also_master' => [
                'expected_exit_code' => Command::INVALID,
                'expected_error'     => 'Error: mustakrakishe_chain_command:test_member_comand command'
                                     . ' is a member of mustakrakishe_chain_command:test_master_comand command chain'
                                     . ' and cannot be executed on its own.'
                                     . PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_member_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand',
                        Command::SUCCESS,
                        'Member success 1!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand'],
                    ],
                    'mustakrakishe_chain_command:test_member_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_master_comand'],
                    ],
                ],
            ],
        ];
    }

    public function chainWithExceptionProvider(): array
    {
        return [
            'run_master_that_throws_exception' => [
                'expected_exit_code' => Command::FAILURE,
                'expected_output'    => '',
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::FAILURE,
                        'Master failed!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => []
                ],
            ],
            'run_master_with_member_that_throws_exception' => [
                'expected_exit_code' => Command::FAILURE,
                'expected_output'    => 'Master success!'.PHP_EOL,
                'command_to_run'     => 'mustakrakishe_chain_command:test_master_comand',
                'app_commands'       => [
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_master_comand',
                        Command::SUCCESS,
                        'Master success!'
                    ),
                    $this->createTestCommand(
                        'mustakrakishe_chain_command:test_member_comand',
                        Command::FAILURE,
                        'Member failed!'
                    ),
                ],
                'chains'             => [
                    'mustakrakishe_chain_command:test_master_comand' => [
                        ['command' => 'mustakrakishe_chain_command:test_member_comand'],
                    ]
                ],
            ],
        ];
    }
}
