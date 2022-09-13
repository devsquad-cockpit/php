<?php

use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Symfony\Component\ErrorHandler\ErrorHandler;

DumpContext::start();
$handler = ErrorHandler::register();
$handler->setExceptionHandler([CockpitErrorHandler::class, 'log']);
