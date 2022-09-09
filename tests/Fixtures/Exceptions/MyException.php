<?php

namespace Cockpit\Php\Tests\Fixtures\Exceptions;

use Exception;

class MyException extends Exception
{
    protected $message = 'This is an exception message';
}
