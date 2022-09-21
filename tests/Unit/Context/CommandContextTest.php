<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Context\CommandContext;
use Cockpit\Php\Tests\TestCase;

class CommandContextTest extends TestCase
{
    /** @test */
    public function it_should_return_a_context_data_when_executing_a_command()
    {
        global $argv;

        $argv = [
            'command'
        ];

        $context = new CommandContext();

        $this->assertArrayContains($context->getContext(), [
            'command'   => 'command',
            'arguments' => [],
        ]);
    }

    /** @test */
    public function it_should_return_a_context_data_when_executing_a_command_with_arguments()
    {
        global $argv;

        $argv = [
            'command',
            '--user=1',
        ];

        $context = new CommandContext();

        $this->assertArrayContains($context->getContext(), [
            'command'   => 'command',
            'arguments' => [
                '--user=1'
            ],
        ]);
    }

    /** @test */
    public function it_should_return_an_empty_array_if_application_is_not_running_in_console()
    {
        global $argv, $consoleFakeReturn;

        $argv = [
            'php command',
            'fake:command',
            '--user=1',
        ];

        $consoleFakeReturn = false;
        $context           = new CommandContext();

        $this->assertEmpty($context->getContext());
    }

    /** @test */
    public function it_should_return_return_an_empty_array_if_arguments_are_empty()
    {
        global $argv;

        $argv = [];

        $context = new CommandContext();

        $this->assertEmpty($context->getContext());
    }
}
