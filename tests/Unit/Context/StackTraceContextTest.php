<?php

use Cockpit\Php\Context\StackTraceContext;
use Cockpit\Php\Tests\Fixtures\Services\MyService;

it('should get stack trace from a exception', function () {
    $exception = null;

    try {
        (new MyService())->handle();
    } catch (Exception $e) {
        $exception = $e;
    }

    $context = (new StackTraceContext($exception))->getContext();

    $mainDir = str_replace('/Unit/Context', '', __DIR__);

    expect($context)
        ->toBeArray()
        ->and($context[0])
        ->toBe([
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
        ])
        ->and($context[1])
        ->toBe([
            'file'              => __DIR__ . '/StackTraceContextTest.php',
            'line'              => 10,
            'function'          => '{closure}',
            'class'             => 'P\Tests\Unit\Context\StackTraceContextTest',
            'application_frame' => true,
            'preview'           => [
                1  => '<?php',
                2  => '',
                3  => 'use Cockpit\Php\Context\StackTraceContext;',
                4  => 'use Cockpit\Php\Tests\Fixtures\Services\MyService;',
                5  => '',
                6  => 'it(\'should get stack trace from a exception\', function () {',
                7  => '    $exception = null;',
                8  => '',
                9  => '    try {',
                10 => '        (new MyService())->handle();',
                11 => '    } catch (Exception $e) {',
                12 => '        $exception = $e;',
                13 => '    }',
                14 => '',
                15 => '    $context = (new StackTraceContext($exception))->getContext();',
                16 => '',
                17 => '    $mainDir = str_replace(\'/Unit/Context\', \'\', __DIR__);',
                18 => '',
                19 => '    expect($context)',
                20 => '        ->toBeArray()',
            ],
        ]);
});
