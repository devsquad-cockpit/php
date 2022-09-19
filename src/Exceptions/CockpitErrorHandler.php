<?php

namespace Cockpit\Php\Exceptions;

use Cockpit\Php\Common\OccurrenceType;
use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Context\StackTraceContext;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CockpitErrorHandler
{
    public function log(Throwable $throwable): void
    {
        $traceContext = new StackTraceContext($throwable);
        $dumpContext  = new DumpContext();

        $data = [
            'exception'   => Str::replace('Symfony\\Component\\ErrorHandler\\', '', get_class($throwable)),
            'message'     => $throwable->getMessage(),
            'file'        => $throwable->getFile(),
            'code'        => $throwable->getCode(),
            'resolved_at' => null,
            'type'        => $this->getExceptionType(),
            'url'         => $this->resolveUrl(),
            'trace'       => $traceContext->getContext(),
            'dump'        => $dumpContext->getContext()
        ];

        $this->send($data);
    }

    protected function resolveUrl(): ?string
    {
        return running_in_console()
            ? null
            : Request::createFromGlobals()->fullUrl();
    }

    protected function getExceptionType(): string
    {
        if (running_in_console()) {
            return OccurrenceType::CLI;
        }

        return OccurrenceType::WEB;
    }

    protected function send($data): void
    {
        try {
            (new Client())->post(getenv('COCKPIT_URL'), [
                'json' => $data
            ]);
        } catch (Throwable $e) {
        }
    }
}
