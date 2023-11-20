# Chain command bundle

A Symfony bundle to run registered chains of console commands.

## Description

The bundle allows to register chains of console commands with one _chain master_ command and one or more _chain member_ commands. After that the master command run in a console will lead to a run of registered command queue one by one. Also it will prohibit a run of the command, that is registered as a member, even if it is also registered as a master of any chain.

## Usage

1. Register your chain in a config file:

```yaml
# config/packages/mustakrakishe-chain-bundle.yaml

mustakrakishe_chain_command:
    chains:

        # master_command:
        #     -   command: member_command
        #         param_key_1: param_value_1
        #         param_key_2: param_value_2

        foo:hello:
            -   command: bar:hi
```

2. Run a master command in a console:

```
$ php app/console foo:hello
Hello from Foo!
Hi from Bar!
```

3. If you try to run a member command, it will be terminated with a message:

```
$ php bin/console bar:hi   
Error: bar:hi command is a member of foo:hello command chain and cannot be executed on its own.
```

## Logging

The bundle logs chain runs:
```log
# var/log/chain-command.log

[2023-11-20 10:45:27] foo:hello is a master command of a command chain that has registered member commands
[2023-11-20 10:45:27] bar:hi registered as a member of foo:hello command chain
[2023-11-20 10:45:27] Executing foo:hello command itself first:
[2023-11-20 10:45:27] Hello from Foo!
[2023-11-20 10:45:27] Executing foo:hello chain members:
[2023-11-20 10:45:27] Hi from Bar!
[2023-11-20 10:45:27] Execution of foo:hello chain completed.
```

## Tests

The bundle is staffed with few [functional tests](bundle/Mustakrakishe/ChainCommandBundle/tests/FunctionalTest.php). You can run them in the console:


```sh
$ php bin/phpunit
```

Also the project contains two test bundles, that provide simple console commands with an output to easy indicate its running:

- foo:hello;
- bar:hi.

So you can test the chain command bundle manually.