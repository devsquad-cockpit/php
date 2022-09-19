<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\HtmlDumper;
use Symfony\Component\VarDumper\Cloner\VarCloner;

it('should mount valid html with header and data', function () {
    $dumper = new HtmlDumper();
    $dumper->setDumpHeader('<foo></foo>');
    $dumper->setDumpBoundaries('<bar>', '</bar>');

    $data = (new VarCloner())->cloneVar("Text dump");

    $this->assertStringMatchesFormat(
        getExpectedHtml(),
        $dumper->dump($data)
    );
});

it('should mount valid html with data', function () {
    $var      = 'foo';
    $expected = '<span class=sf-dump-str title="3 characters">foo</span>';

    $dumper = new HtmlDumper();
    $cloner = new VarCloner();

    $this->assertStringContainsString(
        $expected,
        $dumper->dump($cloner->cloneVar($var))
    );
});

it('should mount valid html with header and data to dump variable', function () {
    $dumper = new HtmlDumper();

    expect($dumper->dumpVariable((new VarCloner())->cloneVar('foo')))->toContain(
        '<span class=sf-dump-str title="3 characters">foo</span>'
    );
});

function getExpectedHtml():string
{
    return
        <<<'EOTXT'
        <foo></foo><bar>"<span class=sf-dump-str title="9 characters">Text dump</span>"
        </bar>
        EOTXT;
}
