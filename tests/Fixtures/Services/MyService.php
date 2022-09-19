<?php

namespace Cockpit\Php\Tests\Fixtures\Services;

use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;

class MyService
{
    public function handle(): void
    {
        throw new MyException();
    }
}
