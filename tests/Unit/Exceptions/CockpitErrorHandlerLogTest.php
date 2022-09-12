<?php

use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Cockpit\Php\Tests\Fixtures\Services\MyService;
use GuzzleHttp\Client;

it('should send error to cockpit server', function () {
    $this->setRunTestInSeparateProcess(true);

    try {
        (new MyService())->handle();
    } catch (Exception $e) {
    }

    mock('overload:' . Client::class)
        ->shouldReceive('post')
        ->times(1);

    CockpitErrorHandler::log($e);
});
