<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Context\CommandContext;

it('should return a context data when executing a command', function () {
    global $argv;

    $argv = [
        'command'
    ];

    $context = new CommandContext();

    expect($context->getContext())
        ->toBe([
            'command'   => 'command',
            'arguments' => [],
        ]);
});

it('should return a context data when executing a command with arguments', function () {
    global $argv;

    $argv = [
        'command',
        '--user=1',
    ];

    $context = new CommandContext();

    expect($context->getContext())
        ->toBe([
            'command'   => 'command',
            'arguments' => [
                '--user=1'
            ],
        ]);
});

it('should return an empty array if application is not running in console', function () {
    global $argv, $consoleFakeReturn;

    $argv = [
        'php command',
        'fake:command',
        '--user=1',
    ];

    $consoleFakeReturn = false;
    $context           = new CommandContext();

    expect($context->getContext())
        ->toBeArray()
        ->toBeEmpty();
});

it('should return return an empty array if arguments are empty', function () {
    global $argv;

    $argv = [];

    $context = new CommandContext();

    expect($context->getContext())
        ->toBeArray()
        ->toBeEmpty();
});
