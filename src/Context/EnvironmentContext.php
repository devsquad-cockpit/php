<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Interfaces\ContextInterface;
use Symfony\Component\HttpFoundation\Request;

class EnvironmentContext implements ContextInterface
{
    protected $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    /** @suppressWarnings(PHPMD.Superglobals) */
    public function getContext(): ?array
    {
        return array_merge([
            'framework_version'     => Cockpit::$frameworkVersion ?? getenv('APP_VERSION'),
            'laravel_locale'        => '',
            'laravel_config_cached' => '',
            'app_debug'             => getenv('APP_DEBUG'),
            'app_env'               => getenv('APP_ENV'),
            'environment_date_time' => date_default_timezone_get(),
            'php_version'           => phpversion(),
            'os_version'            => PHP_OS,
            'server_software'       => !empty($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '',
            'database_version'      => $this->getDatabaseVersion(),
            'browser_version'       => $this->request->headers->get('User-Agent'),
            'node_version'          => $this->runExec('node -v'),
            'npm_version'           => $this->runExec('npm -v'),
        ], Cockpit::getEnvs());
    }

    private function getDatabaseVersion()
    {
        return '';
    }

    private function runExec($command)
    {
        if (($value = @exec($command)) !== '') {
            return $value;
        }

        return 'Not Captured';
    }
}
