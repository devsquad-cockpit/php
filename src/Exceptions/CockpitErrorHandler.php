<?php

namespace Cockpit\Php\Exceptions;

use Cockpit\Php\Context\StackTraceContext;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Monolog\Logger;
use Throwable;

class CockpitErrorHandler
{
    protected int $minimumLogLevel = Logger::ERROR;

    public function setMinimumLogLevel(int $level)
    {
        if (!in_array($level, Logger::getLevels())) {
            throw new InvalidArgumentException('The given log level is not supported');
        }

        $this->minimumLogLevel = $level;
    }

    public function write(array $record): void
    {
        if (!$this->shouldReport($record)) {
            return;
        }

        $this->log(
            $record['context']['exception'],
            Arr::except($record['context'], 'exception')
        );
    }

    protected function shouldReport(array $report): bool
    {
        return $this->hasException($report) && $this->hasValidLogLevel($report);
    }

    protected function hasException(array $report): bool
    {
        return isset($report['context']['exception'])
               && $report['context']['exception'] instanceof Throwable;
    }

    protected function hasValidLogLevel(array $report): bool
    {
        return $report['level'] >= $this->minimumLogLevel;
    }
    
    public static function log(Throwable $throwable)
    {
        $traceContext = new StackTraceContext($throwable);

        $data = [
            'throwable' => [
                'exception'   => Str::replace('Symfony\\Component\\ErrorHandler\\', '', get_class($throwable)),
                'message'     => $throwable->getMessage(),
                'file'        => $throwable->getFile(),
                'code'        => $throwable->getCode(),
                'resolved_at' => null,
            ],
            'traceContext' => $traceContext->getContext(),
        ];

        self::send($data);
    }

    protected static function send($data)
    {
        try {
            $client = new Client();
            $client->post(getenv('COCKPIT_URL'), [
                'body' => $data
            ]);
        } catch (Exception $e) {
        }
    }
}
