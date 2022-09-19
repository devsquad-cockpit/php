<?php

namespace Cockpit\Php\Tests\Fixtures\Context;

use Cockpit\Php\Context\RequestContext;

class RequestContextMock extends RequestContext
{
    public function __construct(
        $request
    ) {
        $this->request = $request;
    }
}
