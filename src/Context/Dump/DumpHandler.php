<?php

namespace Cockpit\Php\Context\Dump;

use Cockpit\Php\Context\DumpContext;
use Symfony\Component\VarDumper\Cloner\VarCloner;

/**
 * @see https://github.com/spatie/laravel-ignition
 */
class DumpHandler
{
    protected $dump;

    public function __construct(DumpContext $dump)
    {
        $this->dump = $dump;
    }

    public function dump($value): void
    {
        $this->dump->record(
            (new VarCloner())->cloneVar($value)
        );
    }
}
