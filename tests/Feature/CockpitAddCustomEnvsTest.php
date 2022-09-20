<?php

namespace Cockpit\Php\Tests\Feature;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Tests\TestCase;

class CockpitAddCustomEnvsTest extends TestCase
{
    /** @test */
    public function it_should_add_the_custom_environments()
    {
        Cockpit::addCustomEnvs(['new-env' => 'custom-env']);
        $payload = Cockpit::getCustomEnvs();

        $this->assertEquals($payload['new-env'], 'custom-env');
    }
}
