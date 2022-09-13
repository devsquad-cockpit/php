<?php

namespace Cockpit\Php\Tests\Feature;

use Cockpit\Php\Cockpit;

it('should add the custom environments', function () {
    Cockpit::addCustomEnvs(['new-env' => 'custom-env']);
    $payload = Cockpit::getCustomEnvs();

    expect($payload)->toBeArray()
        ->toHaveKeys(['new-env'])
        ->and($payload['new-env'])->toBe('custom-env');
});
