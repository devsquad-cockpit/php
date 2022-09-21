<?php

namespace Cockpit\Php\Tests\Unit\Context;

use Cockpit\Php\Context\RequestContext;
use Cockpit\Php\Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Exception\InvalidArgumentException;

class RequestContextTest extends TestCase
{
    /** @test */
    public function it_should_retrieve_basic_request_data()
    {
        $appSession = 'eyJpdiI6IkRIQU1CUHhLS3loNlU5VzNsUHZRcnc9PSIsInZhbHVlIjoiRW5zbnI5N0F0eGQ1dGxmV2h6OU9Ddz09IiwibWFjIjoiZWFmMGZiODUwMWQxY2IzNjI5OGUyYTU1NjUwNDUyZDNiZDk4NjY5YTk5OTk5MTUyZjNmNzI3NmE3NWRhNjcxNCIsInRhZyI6IiJ9';

        $request = Request::create(
            '/update',
            'PUT',
            [],
            ['app_session' => $appSession],
            [],
            ['HTTP_ACCEPT' => 'application/json']
        );

        $context = (new RequestContext($request))->getContext();

        $this->assertArrayContains($context, [
            'request' => [
                'url'    => 'http://localhost/update',
                'method' => 'PUT',
            ],
            'query_string' => [],
            'body'         => [],
            'files'        => [],
            'headers'      => [
                'accept' => ['application/json']
            ],
            'cookies' => [
                'app_session' => $appSession
            ]
        ]);
    }

    /** @test */
    public function it_should_test_if_payload_will_comes_with_query_string()
    {
        $request = Request::create('/update?only_active=1', 'PUT');
        $context = (new RequestContext($request))->getContext();

        $this->assertArrayContains($context, [
            'request' => [
                'url'    => 'http://localhost/update',
                'method' => 'PUT'
            ],
            'query_string' => [
                'only_active' => 1
            ]
        ]);
    }

    /** @test */
    public function it_should_test_if_payload_will_comes_with_body_content()
    {
        $request = Request::create('/update', 'PUT');
        $request->request->add([
            'name'      => 'John Doe',
            'is_active' => false,
        ]);

        $context = (new RequestContext($request))->getContext();

        $this->assertArrayContains($context, [
            'request' => [
                'url'    => 'http://localhost/update',
                'method' => 'PUT',
            ],
            'body' => [
                'name'      => 'John Doe',
                'is_active' => false,
            ]
        ]);
    }

    /** @test */
    public function it_should_test_if_files_are_present_on_payload()
    {
        $file = UploadedFile::fake()->image('avatar.png');

        $request = Request::create('/update', 'PUT', [], [], [
            'avatar' => $file,
        ]);

        $context = (new RequestContext($request))->getContext();

        $this->assertArrayContains($context['files'], [
            'avatar' => [
                'pathname' => $file->getPathname(),
                'size'     => $file->getSize(),
                'mimeType' => $file->getMimeType(),
            ],
        ]);
    }

    /** @test */
    public function it_should_return_an_empty_array_if_files_arent_an_instance_of_UploadedFile()
    {
        $request = Request::create('/update', 'PUT', [], [], [
            'avatar' => [],
        ]);

        $context = (new RequestContext($request))->getContext();

        $this->assertEquals($context['files']['avatar'], []);
    }

    /**
     * @dataProvider
     */
    public function it_should_return_default_file_values_if_exception_occurs(
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

        $context = (new RequestContext($request))->getContext();

        $this->assertArrayContains($context['files'], [
            'avatar' => [
                'pathname' => '/some/string',
                'size'     => $size,
                'mimeType' => $mime,
            ],
        ]);
    }

    public function provider(): array
    {
        return [
            ['getSize', RuntimeException::class, 0, 'image/jpg'],
            ['getMimeType', InvalidArgumentException::class, 1024, 'undefined'],
        ];
    }

    /** @test */
    public function it_should_check_curl_command()
    {
        $appSession = 'eyJpdiI6IkRIQU1CUHhLS3loNlU5VzNsUHZRcnc9PSIsInZhbHVlIjoiRW5zbnI5N0F0eGQ1dGxmV2h6OU9Ddz09IiwibWFjIjoiZWFmMGZiODUwMWQxY2IzNjI5OGUyYTU1NjUwNDUyZDNiZDk4NjY5YTk5OTk5MTUyZjNmNzI3NmE3NWRhNjcxNCIsInRhZyI6IiJ9';

        $request = Request::create(
            '/update',
            'PUT',
            [],
            ['app_session' => $appSession],
        );

        $request->request->add(['name' => 'John Doe', 'is_active' => false]);

        $context = (new RequestContext($request))->getContext();

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

        $this->assertEquals(
            $context['request']['curl'],
            <<<SHELL
    curl "http://localhost/update" \
    -X PUT \
{$headers}{$body}
SHELL
        );
    }

    /** @test */
    public function it_should_check_curl_command_when_application_is_working_with_json()
    {
        $request = Request::create(
            '/update',
            'PUT',
            [],
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        $request->request->add(['name' => 'John Doe', 'is_active' => false]);
        $context = (new RequestContext($request))->getContext();

        $headers = "";

        foreach ($request->headers->all() as $header => $value) {
            $value = implode(',', $value);
            $headers .= "\t-H '{$header}: {$value}' \ \r\n";
        }

        $body = "\t-d '" . json_encode(['name' => 'John Doe', 'is_active' => false]) . "' \ \r\n";

        $this->assertEquals(
            $context['request']['curl'],
            <<<SHELL
    curl "http://localhost/update" \
    -X PUT \
{$headers}{$body}
SHELL
        );
    }

    /** @test */
    public function it_should_return_a_empty_session_collection_if_app_is_running_in_console()
    {
        $request = Request::create('/users');

        $context = (new RequestContext($request))->getContext();

        $this->assertEmpty($context['session']);
    }
}
