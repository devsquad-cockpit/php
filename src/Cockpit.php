<?php

namespace Cockpit\Php;

class Cockpit
{
    protected static $customEnvs = [];

    public static $frameworkVersion;

    public static function addCustomEnvs($callback)
    {
        if (is_callable($callback)) {
            self::$customEnvs[] = $callback;
        }

        if (is_array($callback)) {
            self::$customEnvs[] = function () use ($callback) {
                return $callback;
            };
        }
    }

    public static function getCustomEnvs()
    {
        $envs = [];

        foreach (self::$customEnvs as $func) {
            $envs = array_merge($envs, ($func)());
        }

        return $envs;
    }

    public static function frameworkVersion(string $frameworkVersion)
    {
        self::$frameworkVersion = $frameworkVersion;
    }
}
