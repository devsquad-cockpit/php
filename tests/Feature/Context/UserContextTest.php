<?php

namespace Cockpit\Tests\Unit\Context;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Context\UserContext;
use Cockpit\Php\Tests\TestCase;

class UserContextTest extends TestCase
{
    /** @test */
    public function it_should_retrieve_an_empty_array_if_user_is_unauthenticated()
    {
        $context = (new UserContext())->getContext();

        $this->assertEmpty($context);
    }

    /** @test */
    public function it_should_retrieve_an_user_array_if_user_is_set_on_cockpit_closure()
    {
        $user = [
            'id'    => 1,
            'name'  => 'cockpit user',
            'email' => 'fake@email.com',
            'guard' => 'web'
        ];

        Cockpit::setUser(function () use ($user) {
            return $user;
        }, 'web');

        $context = (new UserContext())->getContext();
        $this->assertEquals($user, $context);
    }
}
