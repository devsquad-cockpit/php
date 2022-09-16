<?php

use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Symfony\Component\ErrorHandler\ErrorHandler;

$handler = ErrorHandler::register();
$handler->setExceptionHandler([new CockpitErrorHandler(), 'log']);
