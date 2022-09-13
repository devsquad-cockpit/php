<?php
namespace Cockpit\Php;

class Cockpit
{
    protected static $addEnvCallbacks = [];
    public static $frameworkVersion;

    public static function addEnvs($callback)
    {
        if (is_callable($callback)) {
            self::$addEnvCallbacks[] = $callback;
        }

        if (is_array($callback)) {
            self::$addEnvCallbacks[] = function () use ($callback) {
                return $callback;
            };
        }
    }

    public static function getEnvs()
    {
        $envs = [];

        foreach (self::$addEnvCallbacks as $func) {
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
