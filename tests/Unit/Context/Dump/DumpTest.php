<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\Dump;
use Cockpit\Php\Tests\TestCase;

class DumpTest extends TestCase
{
    /** @test */
    public function it_should_mount_valid_dump_data()
    {
        $sourceFrame = [
            "file"     => "Cockpit\Tests\Unit\Context\Dump\VarDumper.php",
            "line"     => 123,
            "function" => "dump",
            "class"    => VarDumper::class,
            "type"     => "->"
        ];

        $dump = new Dump($this->getHtmlString(), $sourceFrame['file'], $sourceFrame['line'], 123);

        $this->assertInstanceOf(Dump::class, $dump);
        $this->assertArrayContains($dump->toArray(), [
            'html_dump'   => $this->getHtmlString(),
            'file'        => $sourceFrame['file'],
            'line_number' => $sourceFrame['line'],
            'microtime'   => '123'
        ]);
    }

    public function getHtmlString(): string
    {
        return
        <<<'EOTXT'
        <foo></foo><bar>"<span class=sf-dump-str title="9 characters">Text dump</span>"
        </bar>
        EOTXT;
    }
}
