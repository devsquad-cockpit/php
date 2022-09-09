<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Interfaces\ContextInterface;
use Spatie\Backtrace\Backtrace;
use Spatie\Backtrace\CodeSnippet;
use Spatie\Backtrace\Frame;
use Throwable;

class StackTraceContext implements ContextInterface
{
    protected Throwable $throwable;

    public function __construct(Throwable $throwable)
    {
        $this->throwable = $throwable;
    }

    public function getContext(): array
    {
        $trace = [];

        $backTrace = Backtrace::createForThrowable($this->throwable)
            ->applicationPath(get_base_dir());

        foreach ($backTrace->frames() as $frame) {
            $trace[] = [
                'file'              => $frame->file,
                'line'              => $frame->lineNumber,
                'function'          => $frame->method,
                'class'             => $frame->class,
                'application_frame' => $frame->applicationFrame,
                'preview'           => $this->resolveFilePreview($frame),
            ];
        }

        return $trace;
    }

    protected function resolveFilePreview(Frame $frame): array
    {
        return (new CodeSnippet())
            ->surroundingLine($frame->lineNumber)
            ->snippetLineCount(20)
            ->get($frame->file);
    }
}
