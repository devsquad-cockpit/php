<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Interfaces\ContextInterface;

/**
 * @SuppressWarnings(PHPMD.Superglobals)
 */
class CommandContext implements ContextInterface
{
    public function getContext(): array
    {
        global $argv;

        if (!running_in_console()) {
            return [];
        }

        $arguments = $argv ?? [];

        if (empty($arguments)) {
            return [];
        }

        $command = array_shift($arguments);

        return [
            'command'   => $command,
            'arguments' => $arguments,
        ];
    }
}
