<?php

namespace Cockpit\Php\Exceptions;

use Cockpit\Php\Common\OccurrenceType;
use Cockpit\Php\Context\CommandContext;
use Cockpit\Php\Context\DumpContext;
use Cockpit\Php\Context\EnvironmentContext;
use Cockpit\Php\Context\RequestContext;
use Cockpit\Php\Context\StackTraceContext;
use Cockpit\Php\Context\UserContext;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Throwable;

class CockpitErrorHandler
{
    private $response = null;

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
        $this->log(
            $record['context']['exception'],
            Arr::except($record['context'], 'exception')
        );
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
            $this->response = (new Client())->post(getenv('COCKPIT_URL'), [
                'json' => $data
            ]);
        } catch (ClientException $e) {
            $this->response = $e->getResponse();
            file_put_contents('cockpit.log', $e->getMessage());
        } catch (Exception $e) {
            file_put_contents('cockpit.log', $e->getMessage());
        }
    }

    public function failed(): ?bool
    {
        return $this->response
            ? $this->response->getStatusCode() !== 201
            : null;
    }

    public function reason(): ?string
    {
        return $this->response
            ? "Reason: {$this->response->getStatusCode()} {$this->response->getReasonPhrase()}"
            : null;
    }
}
