<?php

namespace Cockpit\Php\Tests;

class HelpersTest extends TestCase
{
    /** @test */
    public function it_should_return_application_path_when_in_env()
    {
        putenv('APP_PATH=/cockpit/path');

        $this->assertSame('/cockpit/path', get_base_dir());
    }

    /** @test */
    public function it_should_return_application_path_when_not_has_on_env()
    {
        putenv('APP_PATH=');

        $path = dirname(__FILE__, 5);

        $this->assertSame($path, get_base_dir());
    }
}
