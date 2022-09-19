<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\Dump;

it('should mount valid dump data', function () {
    $sourceFrame = [
        "file"     => "Cockpit\Tests\Unit\Context\Dump\VarDumper.php",
        "line"     => 123,
        "function" => "dump",
        "class"    => VarDumper::class,
        "type"     => "->"
    ];

    $dump = new Dump(getHtmlString(), $sourceFrame['file'], $sourceFrame['line'], 123);

    expect($dump)
        ->toBeInstanceOf(Dump::class)
        ->and($dump->toArray())
        ->toBeArray()
        ->toMatchArray([
            'html_dump'   => getHtmlString(),
            'file'        => $sourceFrame['file'],
            'line_number' => $sourceFrame['line'],
            'microtime'   => '123'
        ]);
});

function getHtmlString():string
{
    return
        <<<'EOTXT'
        <foo></foo><bar>"<span class=sf-dump-str title="9 characters">Text dump</span>"
        </bar>
        EOTXT;
}
