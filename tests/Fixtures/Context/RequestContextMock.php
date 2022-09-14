<?php

namespace Cockpit\Php\Tests\Fixtures\Context;

use Cockpit\Php\Context\RequestContext;

class RequestContextMock extends RequestContext
{
    public function __construct(
        $request,
        array $hideFromRequest = [],
        array $hideFromHeaders = [],
        array $hideFromCookies = []
    ) {
        $this->request = $request;

        $this->hideFromRequest = $hideFromRequest;
        $this->hideFromHeaders = $hideFromHeaders;
        $this->hideFromCookies = $hideFromCookies;
    }
}
