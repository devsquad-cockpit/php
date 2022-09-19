<?php

namespace Cockpit\Php\Tests\Feature\Context;

use Cockpit\Php\Context\EnvironmentContext;

it('should return environment context', function () {
    $context = new EnvironmentContext;
    $payload = $context->getContext();

    expect($payload)->toBeArray()
        ->and($payload['framework_version'])->toBe(getenv('APP_VERSION'))
        ->and($payload['laravel_locale'])->toBe('')
        ->and($payload['laravel_config_cached'])->toBe('')
        ->and($payload['app_debug'])->toBe(getenv('APP_DEBUG'))
        ->and($payload['app_env'])->toBe(getenv('APP_ENV'))
        ->and($payload['environment_date_time'])->toBe(date_default_timezone_get())
        ->and($payload['php_version'])->toBe(phpversion())
        ->and($payload['os_version'])->toBe(PHP_OS)
        ->and($payload['server_software'])->toBe('')
        ->and($payload['database_version'])->toBe('')
        ->and($payload['browser_version'])->toBe(null)
        ->and($payload['node_version'])->toBe(runExec('node -v'))
        ->and($payload['npm_version'])->toBe(runExec('npm -v'));
});

function runExec($command)
{
    if (($value = @exec($command)) !== '') {
        return $value;
    }

    return 'Not Captured';
}
