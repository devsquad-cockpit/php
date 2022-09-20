<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\DumpHandler;
use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Tests\TestCase;

class DumpHandlerTest extends TestCase
{
    /** @test */
    public function it_should_be_execute_dump_handler_record_value_at_dump_context()
    {
        $value       = "Text dump";
        $dumpContext = new DumpContext();

        $this->assertCount(0, $dumpContext->getContext());

        $dumpHandler = new DumpHandler($dumpContext);
        $dumpHandler->dump($value);

        $response = $dumpContext->getContext()[0];
        $this->assertEquals(array_keys($response), ['html_dump', 'file', 'line_number', 'microtime']);
        $this->assertStringContainsString($value, $response['html_dump']);
    }
}
