<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Interfaces\ContextInterface;

class UserContext implements ContextInterface
{
    protected $user;

    public function __construct()
    {
        $this->user = Cockpit::getUser();
    }

    public function getContext(): array
    {
        if (running_in_console() || !$this->user) {
            return [];
        }

        return $this->user->toArray();
    }
}
