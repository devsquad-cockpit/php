<?php

namespace Cockpit\Php\Context;

use Cockpit\Php\Interfaces\ContextInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

class RequestContext implements ContextInterface
{
    protected $request;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
    }

    public function getContext(): ?array
    {
        return [
            'request' => [
                'url'    => rtrim(preg_replace('/\?.*/', '', $this->request->getUri()), '/'),
                'method' => $this->request->getMethod(),
                'curl'   => $this->getCurl(),
            ],
            'headers'      => $this->getHeaders(),
            'query_string' => $this->request->query->all(),
            'body'         => $this->getBody(),
            'files'        => $this->getFiles(),
            'session'      => $this->getSession(),
            'cookies'      => $this->getCookies(),
        ];
    }

    protected function getCurl(): string
    {
        return <<<SHELL
    curl "{$this->request->getUri()}" \
    -X {$this->request->getMethod()} \
{$this->getCurlHeaders()}{$this->getCurlBody()}
SHELL;
    }

    protected function getCurlHeaders(): string
    {
        $headers    = "";
        $allHeaders = $this->request->headers->all();

        foreach ($allHeaders as $header => $value) {
            $value = implode(',', $value);
            $headers .= "\t-H '{$header}: {$value}' \ \r\n";
        }

        return $headers;
    }

    protected function getCurlBody(): string
    {
        $body    = "";
        $allBody = $this->getBody();
        $lastKey = array_key_last($allBody);

        if ($this->request->headers->contains('content-type', 'application/json')) {
            return "\t-d '" . json_encode($allBody) . "' \ \r\n";
        }

        foreach ($allBody as $label => $value) {
            $body .= "\t-F '{$label}={$value}'";

            if ($label != $lastKey) {
                $body .= " \ \r\n";
            }
        }

        return $body;
    }

    protected function getBody(): array
    {
        $content = new ParameterBag((array)json_decode($this->request->getContent(), true));
        $input   = $this->request->request ? $this->request->request->all() : [];
        $data    = Arr::except(
            array_merge($content->all(), $input),
            ['_token']
        );

        return $data;
    }

    protected function getFiles(): array
    {
        if (is_null($this->request->files)) {
            return [];
        }

        return $this->mapFiles($this->request->files->all());
    }

    protected function mapFiles(array $files): array
    {
        return array_map(function ($file) {
            if (is_array($file)) {
                return $this->mapFiles($file);
            }

            if (!$file instanceof UploadedFile) {
                return [];
            }

            try {
                $fileSize = $file->getSize();
            } catch (RuntimeException $e) {
                $fileSize = 0;
            }

            try {
                $mimeType = $file->getMimeType();
            } catch (InvalidArgumentException $e) {
                $mimeType = 'undefined';
            }

            return [
                'pathname' => $file->getPathname(),
                'size'     => $fileSize,
                'mimeType' => $mimeType,
            ];
        }, $files);
    }

    protected function getSession(): Collection
    {
        if (!running_in_console() && $this->request->hasSession()) {
            return collect($this->request->getSession()->all())
                ->except('_token');
        }

        return collect([]);
    }

    protected function getCookies(): Collection
    {
        return collect($this->request->cookies->all())
            ->except(['XSRF-TOKEN']);
    }

    protected function getHeaders(): array
    {
        return $this->request->headers->all();
    }
}
