<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Context\DumpContext;
use Mockery;
use Symfony\Component\VarDumper\Cloner\VarCloner;

beforeEach(function () {
    (new DumpContext())->reset();
});

it('should dump context record data with success and get valid context', function () {
    $expectedFile = [
        "file"     => "Teste.php",
        "line"     => 11,
        "function" => "{closure}",
        "class"    => "Illuminate\Routing\RouteFileRegistrar",
        "type"     => "->"
    ];

    $mock = getMockDumpContext($expectedFile);
    $mock->record((new VarCloner())->cloneVar("Text dump"));
    $response = $mock->getContext()[0];

    expect($response)->toBeArray()->toHaveKeys(['html_dump', 'file', 'line_number', 'microtime']);
    expect($response['html_dump'])
        ->toBeString()
        ->toContain(getHtmlString())
        ->and($response['file'])
        ->toBeString()
        ->toEqual($expectedFile['file'])
        ->and($response['line_number'])
        ->toBeInt()
        ->toEqual($expectedFile['line']);
});

it('should dump context record data with empty source frame return', function () {
    $mock = getMockDumpContext();
    $mock->record((new VarCloner())->cloneVar("Text dump"));
    $response = $mock->getContext()[0];

    expect($response)
        ->toBeArray()
        ->toHaveKeys(['html_dump', 'file', 'line_number', 'microtime'])
        ->and($response['html_dump'])
        ->toBeString()
        ->toContain(getHtmlString())
        ->and($response['file'])
        ->toBeString()
        ->toEqual("")
        ->and($response['line_number'])
        ->toBeInt()
        ->toEqual(0);
});

it('should dump context created with empty data', function () {
    expect((new DumpContext())->getContext())
        ->toBeEmpty()
        ->toBeArray()
        ->toHaveCount(0);
});

it('should dump context reset call set empty data', function () {
    $dumpContext = new DumpContext();
    $dumpContext->record((new VarCloner())->cloneVar("Text dump"));

    expect($dumpContext->getContext())
        ->toBeArray()
        ->toHaveCount(1);

    $dumpContext->reset();

    expect($dumpContext->getContext())
        ->toBeEmpty()
        ->toBeArray()
        ->toHaveCount(0);
});

function getHtmlString(): string
{
    return
        <<<'EOTXT'
        <span class=sf-dump-str title="9 characters">Text dump</span>
        EOTXT;
}

function getMockDumpContext(?array $data = null)
{
    return Mockery::mock(DumpContext::class)
        ->makePartial()
        ->shouldAllowMockingProtectedMethods()
        ->shouldReceive('findSourceFrame')
        ->andReturn($data)
        ->getMock();
}
