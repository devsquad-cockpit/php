<?php

namespace Cockpit\Php\Tests;

use Cockpit\Php\Context\StackTraceContext;
use Cockpit\Php\Tests\Fixtures\Services\MyService;
use Exception;

class StackTraceContextTest extends TestCase
{
    /** @test */
    public function it_should_get_stack_trace_from_a_exception()
    {
        $exception = null;

        try {
            (new MyService())->handle();
        } catch (Exception $e) {
            $exception = $e;
        }

        $context = (new StackTraceContext($exception))->getContext();

        $mainDir = str_replace('/Unit/Context', '', __DIR__);

        $this->assertEquals(
            $context[0],
            [
                'file'              => $mainDir . '/Fixtures/Services/MyService.php',
                'line'              => 11,
                'function'          => 'handle',
                'class'             => MyService::class,
                'application_frame' => true,
                'preview'           => [
                    1  => '<?php',
                    2  => '',
                    3  => 'namespace Cockpit\Php\Tests\Fixtures\Services;',
                    4  => '',
                    5  => 'use Cockpit\Php\Tests\Fixtures\Exceptions\MyException;',
                    6  => '',
                    7  => 'class MyService',
                    8  => '{',
                    9  => '    public function handle(): void',
                    10 => '    {',
                    11 => '        throw new MyException();',
                    12 => '    }',
                    13 => '}',
                    14 => '',
                ],
            ]
        );

        $this->assertEquals($context[1], [
            'file'              => __DIR__ . '/StackTraceContextTest.php',
            'line'              => 17,
            'function'          => 'it_should_get_stack_trace_from_a_exception',
            'class'             => 'Cockpit\Php\Tests\StackTraceContextTest',
            'application_frame' => true,
            'preview'           => [
                7  => 'use Exception;',
                8  => '',
                9  => 'class StackTraceContextTest extends TestCase',
                10 => '{',
                11 => '    /** @test */',
                12 => '    public function it_should_get_stack_trace_from_a_exception()',
                13 => '    {',
                14 => '        $exception = null;',
                15 => '',
                16 => '        try {',
                17 => '            (new MyService())->handle();',
                18 => '        } catch (Exception $e) {',
                19 => '            $exception = $e;',
                20 => '        }',
                21 => '',
                22 => '        $context = (new StackTraceContext($exception))->getContext();',
                23 => '',
                24 => '        $mainDir = str_replace(\'/Unit/Context\', \'\', __DIR__);',
                25 => '',
                26 => '        $this->assertEquals(',
            ],
        ]);
    }
}
