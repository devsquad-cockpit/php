<?php

namespace Cockpit\Php\Tests\Unit\Context\Dump;

use Cockpit\Php\Context\Dump\MultiDumpHandler;

it('should be add multiple callable function at multidump handler and execute all functions', function () {
    $multiDumpHandler = new MultiDumpHandler();
    $multiDumpHandler->addHandler(function ($var) {
        var_dump('call one ' . $var);
    });

    $multiDumpHandler->addHandler(function ($var) {
        var_dump('call two ' . $var);
    });

    expect($multiDumpHandler->getHandlers())
        ->toBeArray()
        ->toHaveCount(2)
        ->and($multiDumpHandler->getHandlers()[0])
        ->toBeCallable();

    ob_start();
    $multiDumpHandler->dump("Dump to test");

    expect(ob_get_clean())
        ->toContain('string(21) "call one Dump to test"')
        ->toContain('string(21) "call two Dump to test"');
});
