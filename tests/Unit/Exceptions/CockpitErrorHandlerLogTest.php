<?php

namespace Cockpit\Php\Tests;

use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;
use Mockery;

class CockpitErrorHandlerLogTest extends TestCase
{
    /** @test */
    public function it_should_send_error_to_cockpit_serve()
    {
        $cockpitUrl = 'http://cockpit/webhook';
        putenv('COCKPIT_URL=' . $cockpitUrl);

        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post')
            ->withArgs(function ($uri) use ($cockpitUrl) {
                return $uri == $cockpitUrl;
            })->times(1);

        (new CockpitErrorHandler())->log(new MyException());
    }
}
