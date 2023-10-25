<?php

namespace Cockpit\Php\Exceptions;

use Cockpit\Php\Common\OccurrenceType;
use Cockpit\Php\Context\CommandContext;
use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Context\EnvironmentContext;
use Cockpit\Php\Context\RequestContext;
use Cockpit\Php\Context\StackTraceContext;
use Cockpit\Php\Context\UserContext;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class CockpitErrorHandler
{
    private $response = null;

    public $failed = false;

    public function log(Throwable $throwable): array
    {
        $traceContext       = new StackTraceContext($throwable);
        $dumpContext        = new DumpContext();
        $environmentContext = new EnvironmentContext();
        $requestContext     = new RequestContext();
        $userContext        = new UserContext();
        $commandContext     = new CommandContext();

        $data = [
            'exception'   => Str::replace('Symfony\\Component\\ErrorHandler\\', '', get_class($throwable)),
            'message'     => $throwable->getMessage(),
            'file'        => $throwable->getFile(),
            'code'        => $throwable->getCode(),
            'resolved_at' => null,
            'type'        => $this->getExceptionType(),
            'url'         => $this->resolveUrl(),
            'trace'       => $traceContext->getContext(),
            'dump'        => $dumpContext->getContext(),
            'environment' => $environmentContext->getContext(),
            'request'     => $requestContext->getContext(),
            'user'        => $userContext->getContext(),
            'command'     => $commandContext->getContext()
        ];

        $this->send($data);

        return $data;
    }

    public function write(array $record): void
    {
        $this->log($record['context']['exception']);
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
            $captureUrl     = preg_replace('#(?<!:)/+#im', '/', getenv('COCKPIT_DOMAIN') . '/api/capture');
            $this->response = (new Client([
                'headers' => ['X-COCKPIT-TOKEN' => getenv('COCKPIT_TOKEN')]
            ]))->post($captureUrl, [
                'json'        => $data,
                'http_errors' => false
            ]);

            $this->failed = $this->response->getStatusCode() !== 201;
        } catch (Throwable $e) {
            error_log($e->getMessage(), $e->getCode());
        }
    }

    public function reason(): ?string
    {
        return $this->response
            ? "Reason: {$this->response->getStatusCode()} {$this->response->getReasonPhrase()}"
            : null;
    }
}
