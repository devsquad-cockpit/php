<?php

use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;
use GuzzleHttp\Client;

it('should send error to cockpit server', function () {
    $cockpitUrl = 'http://cockpit/webhook';
    putenv('COCKPIT_URL=' . $cockpitUrl);

    Mockery::mock('overload:' . Client::class)
        ->shouldReceive('post')
        ->withArgs(function ($uri) use ($cockpitUrl) {
            return $uri == $cockpitUrl;
        })->times(1);

    (new CockpitErrorHandler)->log(new MyException());
});
