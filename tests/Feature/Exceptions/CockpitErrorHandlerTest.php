<?php

namespace Cockpit\Php\Tests\Feature\Exceptions;

use Cockpit\Php\Common\OccurrenceType;
use Cockpit\Php\Exceptions\CockpitErrorHandler;
use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;
use Cockpit\Php\Tests\TestCase;
use Mockery;

class CockpitErrorHandlerTest extends TestCase
{
    /** @test */
    public function it_should_send_error_to_cockpit_serve()
    {
        $cockpitUrl = 'http://cockpit/webhook';
        putenv('COCKPIT_DOMAIN=' . $cockpitUrl);

        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post')
            ->withArgs(function ($uri) use ($cockpitUrl) {
                return $uri == $cockpitUrl;
            })->times(1);

        (new CockpitErrorHandler())->log(new MyException());
    }

    /** @test */
    public function it_should_send_error_to_cockpit_serve_without_url()
    {
        global $consoleFakeReturn;

        $consoleFakeReturn = true;
        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post');

        $data = (new CockpitErrorHandler())->log(new MyException());
        $this->assertNull($data['url']);
    }

    /** @test */
    public function it_should_send_error_to_cockpit_with_type_cli()
    {
        global $consoleFakeReturn;

        $consoleFakeReturn = true;
        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post');

        $data = (new CockpitErrorHandler())->log(new MyException());
        $this->assertSame($data['type'], OccurrenceType::CLI);
    }
}
