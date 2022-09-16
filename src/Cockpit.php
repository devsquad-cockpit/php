<?php

namespace Cockpit\Php;

use Closure;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Request;

class Cockpit
{
    protected static $userClosure;

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

        if (is_callable(self::$userClosure)) {
            return call_user_func(self::$userClosure, $request);
        }

        return null;
    }
}
