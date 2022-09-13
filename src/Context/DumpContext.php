<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Context\Dump\Dump;
use Cockpit\Php\Context\Dump\DumpHandler;
use Cockpit\Php\Context\Dump\HtmlDumper;
use Cockpit\Php\Context\Dump\MultiDumpHandler;
use Cockpit\Php\Interfaces\ContextInterface;
use Cockpit\Php\Interfaces\RecorderInterface;
use Illuminate\Support\Arr;
use ReflectionMethod;
use ReflectionProperty;
use Symfony\Component\VarDumper\Cloner\Data;
use Symfony\Component\VarDumper\VarDumper;

/**
 * @see https://github.com/spatie/laravel-ignition
 */
class DumpContext implements ContextInterface, RecorderInterface
{
    protected static array $dumps = [];

    protected static bool $registeredHandler = false;

    public static function start(): DumpContext
    {
        $multiDumpHandler = new MultiDumpHandler();
        $dumpContext      = new self;

        if (!self::$registeredHandler) {
            static::$registeredHandler = true;

            self::ensureOriginalHandlerExists();

            $originalHandler = VarDumper::setHandler(fn ($dumpedVariable) => $multiDumpHandler->dump($dumpedVariable));

            $multiDumpHandler->addHandler($originalHandler);
            $multiDumpHandler->addHandler(fn ($var) => (new DumpHandler($dumpContext))->dump($var));
        }

        return $dumpContext;
    }

    public function record(Data $data)
    {
        $backtrace   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 11);
        $sourceFrame = $this->findSourceFrame($backtrace);

        $file       = (string)Arr::get($sourceFrame, 'file');
        $lineNumber = (int)Arr::get($sourceFrame, 'line');

        $htmlDump = (new HtmlDumper())->dump($data);

        self::$dumps[] = new Dump($htmlDump, $file, $lineNumber);
    }

    public static function reset()
    {
        self::$dumps = [];
    }

    public function getContext(): ?array
    {
        $dumps = [];

        foreach (self::$dumps as $dump) {
            $dumps[] = $dump->toArray();
        }

        return $dumps;
    }

    protected static function ensureOriginalHandlerExists(): void
    {
        $reflectionProperty = new ReflectionProperty(VarDumper::class, 'handler');
        $reflectionProperty->setAccessible(true);

        $handler = $reflectionProperty->getValue();

        if (!$handler) {
            $reflectionMethod = new ReflectionMethod(VarDumper::class, 'register');
            $reflectionMethod->setAccessible(true);
            $reflectionMethod->invoke(null);
        }
    }

    protected function findSourceFrame(array $stackTrace): ?array
    {
        $seenVarDumper = false;

        foreach ($stackTrace as $frame) {
            if (Arr::get($frame, 'class') === VarDumper::class && Arr::get($frame, 'function') === 'dump') {
                $seenVarDumper = true;

                continue;
            }

            if (!$seenVarDumper) {
                continue;
            }

            return $frame;
        }

        return null;
    }
}
