<?php

use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;
use GuzzleHttp\Client;

it('should send error to cockpit server', function () {
    Mockery::mock('overload:' . Client::class)
        ->shouldReceive('post')
        ->times(1);

    CockpitErrorHandler::log(new MyException());
});
