<?php

namespace Cockpit\Php;

use Closure;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class Cockpit
{
    protected static $userClosure;

    protected static $customEnvs = [];

    public static $frameworkVersion;

    public static function setUser(Closure $closure, string $guard = null)
    {
        self::$userClosure = function (Request $request) use ($closure, $guard) {
            $user = call_user_func($closure, $request);

            if (is_array($user) || is_object($user)) {
                $user = collect($user)->put('guard', $guard);

                return $user;
            }

            return null;
        };
    }

    public static function getUser(): ?Collection
    {
        $request = Request::createFromGlobals();

        if (self::$userClosure) {
            return call_user_func(self::$userClosure, $request);
        }

        return null;
    }

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
