<?php

namespace Cockpit\Php\Tests;

use Illuminate\Support\Arr;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase as FrameworkTestCase;

class TestCase extends FrameworkTestCase
{
    use MockeryPHPUnitIntegration;

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function assertArrayContains($actual, array $expected): self
    {
        collect(Arr::dot($expected))->map(function ($value, $key) use ($actual) {
            Assert::assertEquals(Arr::get($actual, $key), $value);
        });

        return $this;
    }
}
