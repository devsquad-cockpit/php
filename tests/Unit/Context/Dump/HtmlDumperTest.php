<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\HtmlDumper;
use Cockpit\Php\Tests\TestCase;
use Symfony\Component\VarDumper\Cloner\VarCloner;

class HtmlDumperTest extends TestCase
{
    /** @test */
    public function it_should_mount_valid_html_with_header_and_data()
    {
        $dumper = new HtmlDumper();
        $dumper->setDumpHeader('<foo></foo>');
        $dumper->setDumpBoundaries('<bar>', '</bar>');

        $data = (new VarCloner())->cloneVar("Text dump");

        $this->assertStringMatchesFormat(
            $this->getExpectedHtml(),
            $dumper->dump($data)
        );
    }

    /** @test */
    public function it_should_mount_valid_html_with_data()
    {
        $var      = 'foo';
        $expected = '<span class=sf-dump-str title="3 characters">foo</span>';

        $dumper = new HtmlDumper();
        $cloner = new VarCloner();

        $this->assertStringContainsString(
            $expected,
            $dumper->dump($cloner->cloneVar($var))
        );
    }

    /** @test */
    public function it_should_mount_valid_html_with_header_and_data_to_dump_variable()
    {
        $dumper = new HtmlDumper();

        $this->assertStringContainsString(
            '<span class=sf-dump-str title="3 characters">foo</span>',
            $dumper->dumpVariable((new VarCloner())->cloneVar('foo'))
        );
    }

    public function getExpectedHtml(): string
    {
        return
        <<<'EOTXT'
        <foo></foo><bar>"<span class=sf-dump-str title="9 characters">Text dump</span>"
        </bar>
        EOTXT;
    }
}
