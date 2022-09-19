<?php

namespace Cockpit\Tests\Unit\Context;

use Cockpit\Php\Cockpit;
use Cockpit\Php\Context\UserContext;

it('should retrieve an empty array if user is unauthenticated', function () {
    $context = (new UserContext())->getContext();

    expect($context)
        ->toBeArray()
        ->toBeEmpty();
});

it('should retrieve an user array if user is set on cockpit closure', function () {
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
});
