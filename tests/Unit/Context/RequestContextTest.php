<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Tests\Fixtures\Context\RequestContextMock;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

it('should retrieve basic request data', function () {
    $appSession = 'eyJpdiI6IkRIQU1CUHhLS3loNlU5VzNsUHZRcnc9PSIsInZhbHVlIjoiRW5zbnI5N0F0eGQ1dGxmV2h6OU9Ddz09IiwibWFjIjoiZWFmMGZiODUwMWQxY2IzNjI5OGUyYTU1NjUwNDUyZDNiZDk4NjY5YTk5OTk5MTUyZjNmNzI3NmE3NWRhNjcxNCIsInRhZyI6IiJ9';

    $request = Request::create(
        '/update',
        'PUT',
        [],
        ['app_session' => $appSession],
        [],
        ['HTTP_ACCEPT' => 'application/json']
    );

    $context = (new RequestContextMock($request))->getContext();

    expect($context)
        ->toBeArray()
        ->not->toBeEmpty()
        ->and($context['request']['url'])->toBe('http://localhost/update')
        ->and($context['request']['method'])->toBe('PUT')
        ->and($context['headers']['accept'][0])->toBe('application/json')
        ->and($context['query_string'])->toBeArray()->toBeEmpty()
        ->and($context['body'])->toBeArray()->toBeEmpty()
        ->and($context['files'])->toBeArray()->toBeEmpty()
        ->and($context['cookies'])->toBeInstanceOf(Collection::class)
        ->and($context['cookies']['app_session'])->toBe($appSession);
});

it('should test if payload will comes with query string', function () {
    $request = Request::create('/update?only_active=1', 'PUT');
    $context = (new RequestContextMock($request))->getContext();

    expect($context['request']['url'])->toBe('http://localhost/update')
        ->and($context['request']['method'])->toBe('PUT')
        ->and($context['query_string'])->toBe(['only_active' => '1']);
});

it('should test if payload will comes with body content', function () {
    $request = Request::create('/update', 'PUT');
    $request->request->add([
        'name'      => 'John Doe',
        'is_active' => false,
    ]);

    $context = (new RequestContextMock($request))->getContext();

    expect($context['request']['url'])->toBe('http://localhost/update')
        ->and($context['request']['method'])->toBe('PUT')
        ->and($context['body'])->toBeArray()->toBe([
            'name'      => 'John Doe',
            'is_active' => false,
        ]);
});

it('should test if files are present on payload', function () {
    $file = UploadedFile::fake()->image('avatar.png');

    $request = Request::create('/update', 'PUT', [], [], [
        'avatar' => $file,
    ]);

    $context = (new RequestContextMock($request))->getContext();

    expect($context['files'])->toBe([
        'avatar' => [
            'pathname' => $file->getPathname(),
            'size'     => $file->getSize(),
            'mimeType' => $file->getMimeType(),
        ],
    ]);
});

it('should return an empty array if files arent an instance of UploadedFile', function () {
    $request = Request::create('/update', 'PUT', [], [], [
        'avatar' => [],
    ]);

    $context = (new RequestContextMock($request))->getContext();

    expect($context['files'])->toBe([
        'avatar' => [],
    ]);
});

it(
    'it should return default file values if exception occurs',
    function (
        $method,
        $exception,
        $size,
        $mime
    ) {
        $file = Mockery::mock(
            UploadedFile::class,
            function (MockInterface $mock) use ($method, $exception, $size, $mime) {
                $mock->shouldReceive('getPathname')->andReturn('/some/string');

                if ($method == 'getSize') {
                    $mock->shouldReceive('getMimeType')->andReturn($mime);
                } else {
                    $mock->shouldReceive('getSize')->andReturn($size);
                }

                $mock->shouldReceive($method)->andThrow($exception);
            }
        )->makePartial();

        $request = Request::create('/update', 'PUT', [], [], [
            'avatar' => $file,
        ]);

        $context = (new RequestContextMock($request))->getContext();

        expect($context['files'])->toBe([
            'avatar' => [
                'pathname' => '/some/string',
                'size'     => $size,
                'mimeType' => $mime,
            ],
        ]);
    }
)->with([
    ['getSize', RuntimeException::class, 0, 'image/jpg'],
    ['getMimeType', InvalidArgumentException::class, 1024, 'undefined'],
]);

it('should check cURL command', function () {
    $appSession = 'eyJpdiI6IkRIQU1CUHhLS3loNlU5VzNsUHZRcnc9PSIsInZhbHVlIjoiRW5zbnI5N0F0eGQ1dGxmV2h6OU9Ddz09IiwibWFjIjoiZWFmMGZiODUwMWQxY2IzNjI5OGUyYTU1NjUwNDUyZDNiZDk4NjY5YTk5OTk5MTUyZjNmNzI3NmE3NWRhNjcxNCIsInRhZyI6IiJ9';

    $request = Request::create(
        '/update',
        'PUT',
        [],
        ['app_session' => $appSession],
    );

    $request->request->add(['name' => 'John Doe', 'is_active' => false]);

    $context = (new RequestContextMock($request))->getContext();

    $headers = "";

    foreach ($request->headers->all() as $header => $value) {
        $value = implode(',', $value);
        $headers .= "\t-H '{$header}: {$value}' \ \r\n";
    }

    $body    = "";
    $allBody = $request->request->all();
    $lastKey = array_key_last($allBody);

    foreach ($allBody as $label => $value) {
        $body .= "\t-F '{$label}={$value}'";

        if ($label != $lastKey) {
            $body .= " \ \r\n";
        }
    }

    expect($context['request']['curl'])
        ->toBe(
            <<<SHELL
    curl "http://localhost/update" \
    -X PUT \
{$headers}{$body}
SHELL
        );
});

it('should check cURL command when application is working with json', function () {
    $request = Request::create(
        '/update',
        'PUT',
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'application/json']
    );

    $request->request->add(['name' => 'John Doe', 'is_active' => false]);
    $context = (new RequestContextMock($request))->getContext();

    $headers = "";

    foreach ($request->headers->all() as $header => $value) {
        $value = implode(',', $value);
        $headers .= "\t-H '{$header}: {$value}' \ \r\n";
    }

    $body = "\t-d '" . json_encode(['name' => 'John Doe', 'is_active' => false]) . "' \ \r\n";

    expect($context['request']['curl'])
        ->toBe(
            <<<SHELL
    curl "http://localhost/update" \
    -X PUT \
{$headers}{$body}
SHELL
        );
});

it('should return a empty session collection if app is running in console', function () {
    $request = Request::create('/users');
    
    $context = (new RequestContextMock($request))->getContext();

    expect($context['session'])->toBeEmpty();
});
