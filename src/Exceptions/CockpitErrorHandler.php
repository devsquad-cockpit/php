<?php

namespace Cockpit\Php\Exceptions;

use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Context\StackTraceContext;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Throwable;

class CockpitErrorHandler
{
    public static function log(Throwable $throwable)
    {
        $traceContext = new StackTraceContext($throwable);
        $dumpContext  = new DumpContext;

        $data = [
            'throwable' => [
                'exception'   => Str::replace('Symfony\\Component\\ErrorHandler\\', '', get_class($throwable)),
                'message'     => $throwable->getMessage(),
                'file'        => $throwable->getFile(),
                'code'        => $throwable->getCode(),
                'resolved_at' => null,
            ],
            'traceContext' => $traceContext->getContext(),
            'dumpContext'  => $dumpContext->getContext()
        ];

        self::send($data);
    }

    protected static function send($data)
    {
        try {
            $client = new Client();
            $client->post(getenv('COCKPIT_URL'), [
                'json' => $data
            ]);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }
    }
}
