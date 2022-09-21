<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\MultiDumpHandler;
use Cockpit\Php\Tests\TestCase;

class MultiDumpHandlerTest extends TestCase
{
    /** @test */
    public function it_should_be_add_multiple_callable_function_at_multidump_handler_and_execute_all_functions()
    {
        $multiDumpHandler = new MultiDumpHandler();
        $multiDumpHandler->addHandler(function ($var) {
            var_dump('call one ' . $var);
        });

        $multiDumpHandler->addHandler(function ($var) {
            var_dump('call two ' . $var);
        });

        $this->assertCount(2, $multiDumpHandler->getHandlers());
        $this->assertIsCallable($multiDumpHandler->getHandlers()[0]);

        ob_start();
        $multiDumpHandler->dump("Dump to test");
        $context = ob_get_clean();

        $this->assertStringContainsString('string(21) "call one Dump to test"', $context);
        $this->assertStringContainsString('string(21) "call two Dump to test"', $context);
    }
}
