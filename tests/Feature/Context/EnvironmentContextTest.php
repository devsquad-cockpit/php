<?php

namespace Cockpit\Php\Tests\Feature\Context;

use Cockpit\Php\Context\EnvironmentContext;
use Cockpit\Php\Tests\TestCase;

class EnvironmentContextTest extends TestCase
{
    /** @test */
    public function it_should_return_environment_context()
    {
        $context = new EnvironmentContext();
        $payload = $context->getContext();

        $this->assertArrayContains($payload, [
            'framework_version'     => getenv('APP_VERSION'),
            'laravel_locale'        => '',
            'laravel_config_cached' => '',
            'app_debug'             => getenv('APP_DEBUG'),
            'app_env'               => getenv('APP_ENV'),
            'environment_date_time' => date_default_timezone_get(),
            'php_version'           => phpversion(),
            'os_version'            => PHP_OS,
            'server_software'       => '',
            'database_version'      => '',
            'browser_version'       => null,
            'node_version'          => $this->runExec('node -v'),
            'npm_version'           => $this->runExec('npm -v'),
        ]);
    }

    public function runExec($command)
    {
        if (($value = @exec($command)) !== '') {
            return $value;
        }

        return 'Not Captured';
    }
}
