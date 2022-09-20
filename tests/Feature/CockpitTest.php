<?php

namespace Cockpit\Php\Tests\Feature;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Tests\TestCase;

class CockpitTest extends TestCase
{
    /** @test */
    public function it_should_add_the_custom_environments()
    {
        Cockpit::addCustomEnvs(['new-env' => 'custom-env']);

        $payload = Cockpit::getCustomEnvs();

        $this->assertEquals($payload['new-env'], 'custom-env');
    }

    /** @test */
    public function it_should_add_the_custom_environments_with_closure()
    {
        Cockpit::addCustomEnvs(function () {
            return ['new-env' => 'custom-env'];
        });

        $payload = Cockpit::getCustomEnvs();

        $this->assertEquals($payload['new-env'], 'custom-env');
    }

    /** @test */
    public function it_should_return_the_user_without_guard()
    {
        $data = ['name' => 'cockpit', 'email' => 'fake@email.com'];

        Cockpit::setUser(function () use ($data) {
            return $data;
        });

        $user = Cockpit::getUser();

        $this->assertArrayContains($user->toArray(), $data);
    }

    /** @test */
    public function it_should_return_the_user_with_guard()
    {
        $data = ['name' => 'cockpit', 'email' => 'fake@email.com'];

        Cockpit::setUser(function () use ($data) {
            return $data;
        }, 'test');

        $user = Cockpit::getUser();

        $this->assertArrayContains($user->toArray(), array_merge($data, ['guard' => 'test']));
    }

    /** @test */
    public function it_should_return_the_null_user_when_data_is_not_array()
    {
        Cockpit::setUser(function () {
            return 'cockpit-user';
        });

        $user = Cockpit::getUser();

        $this->assertNull($user);
    }

    /** @test */
    public function it_should_return_the_framework_version()
    {
        $vesrion = 'V' . rand(1, 5);

        Cockpit::frameworkVersion($vesrion);

        $this->assertSame($vesrion, Cockpit::$frameworkVersion);
    }
}
