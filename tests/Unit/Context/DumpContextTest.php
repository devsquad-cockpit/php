<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Tests\TestCase;
use Mockery;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class DumpContextTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        (new DumpContext())->reset();
    }

    /** @test */
    public function it_should_dump_context_record_data_with_success_and_get_valid_context()
    {
        $expectedFile = [
            "file"     => "Teste.php",
            "line"     => 11,
            "function" => "{closure}",
            "class"    => "Illuminate\Routing\RouteFileRegistrar",
            "type"     => "->"
        ];

        $mock = $this->getMockDumpContext($expectedFile);
        $mock->record((new VarCloner())->cloneVar("Text dump"));
        $response = $mock->getContext()[0];

        $this->assertEquals(array_keys($response), ['html_dump', 'file', 'line_number', 'microtime']);
        $this->assertStringContainsString($this->getHtmlString(), $response['html_dump']);
        $this->assertEquals($response['file'], $expectedFile['file']);
        $this->assertEquals($response['line_number'], $expectedFile['line']);
    }

    /** @test */
    public function it_should_dump_context_record_data_with_empty_source_frame_return()
    {
        $mock = $this->getMockDumpContext();
        $mock->record((new VarCloner())->cloneVar("Text dump"));
        $response = $mock->getContext()[0];

        $this->assertEquals(array_keys($response), ['html_dump', 'file', 'line_number', 'microtime']);
        $this->assertStringContainsString($this->getHtmlString(), $response['html_dump']);
        $this->assertEquals($response['file'], '');
        $this->assertEquals($response['line_number'], 0);
    }

    public function getHtmlString(): string
    {
        return
        <<<'EOTXT'
        <span class=sf-dump-str title="9 characters">Text dump</span>
        EOTXT;
    }

    public function getMockDumpContext(?array $data = null)
    {
        return Mockery::mock(DumpContext::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods()
        ->shouldReceive('findSourceFrame')
        ->andReturn($data)
        ->getMock();
    }

    /** @test */
    public function it_should_dump_context_created_with_empty_data()
    {
        $context = (new DumpContext())->getContext();

        $this->assertCount(0, $context);
        $this->assertIsArray($context);
    }

    /** @test */
    public function it_should_dump_context_reset_call_set_empty_data()
    {
        $dumpContext = new DumpContext();
        $dumpContext->record((new VarCloner())->cloneVar("Text dump"));

        $this->assertCount(1, $dumpContext->getContext());
        $dumpContext->reset();

        $this->assertCount(0, $dumpContext->getContext());
    }
}
