<?php
namespace Cockpit\Php;

class Cockpit
{
    protected static $customEnvs = [];
    public static $frameworkVersion;

    public static function addEnvs($callback)
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

    public static function getEnvs()
    {
        $envs = [];

        foreach (self::$customEnvs as $func) {
            $envs = array_merge($envs, ($func)());
        }

        return $envs;
    }

    public function frameworkVersion(string $frameworkVersion)
    {
        $this->frameworkVersion = $frameworkVersion;

        return $this;
    }
}
