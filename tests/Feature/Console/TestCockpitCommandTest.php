<?php

namespace Cockpit\Php\Tests\Feature\Console;

use Cockpit\Php\Commands\TestCockpitCommand;
use Cockpit\Php\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Mockery;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command as Status;
use Symfony\Component\Console\Tester\CommandTester;

class TestCockpitCommandTest extends TestCase
{
    protected function execute(): CommandTester
    {
        $application = new Application();
        $application->add(new TestCockpitCommand());

        $command       = $application->find('test');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'command' => $command->getName()
        ]);

        return $commandTester;
    }

    /** @test */
    public function it_should_send_cockpit_test_command(): void
    {
        putenv('COCKPIT_URL=http://app.test/webhook');

        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post')
            ->andReturn(new Response(201));

        $commandTester = $this->execute();

        $this->assertSame(Status::SUCCESS, $commandTester->getStatusCode());
        $this->assertStringContainsString('Cockpit reached successfully. We sent a test Exception that has been registered.', $commandTester->getDisplay());
    }

    /** @test */
    public function it_should_notice_when_isnt_able_to_send_test_when_route_is_empty(): void
    {
        putenv('COCKPIT_URL=');

        $commandTester = $this->execute();

        $this->assertSame(Status::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('You must fill COCKPIT_URL env with a valid cockpit endpoint', $commandTester->getDisplay());
    }

    /** @test */
    public function it_should_return_an_error_message(): void
    {
        putenv('COCKPIT_URL=http://app.test/wrong-url');

        Mockery::mock('overload:' . Client::class)
            ->shouldReceive('post')
            ->andReturn(new Response(404));

        $commandTester = $this->execute();

        $output = $commandTester->getDisplay();

        $this->assertSame(Status::FAILURE, $commandTester->getStatusCode());
        $this->assertStringContainsString('We couldn\'t reach Cockpit Server at http://app.test/wrong-url', $output);
        $this->assertStringContainsString('Reason: 404 Not Found', $output);
    }
}
