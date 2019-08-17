<?php
/**
 *    ______            __             __
 *   / ____/___  ____  / /__________  / /
 *  / /   / __ \/ __ \/ __/ ___/ __ \/ /
 * / /___/ /_/ / / / / /_/ /  / /_/ / /
 * \______________/_/\__/_/   \____/_/
 *    /   |  / / /_
 *   / /| | / / __/
 *  / ___ |/ / /_
 * /_/ _|||_/\__/ __     __
 *    / __ \___  / /__  / /____
 *   / / / / _ \/ / _ \/ __/ _ \
 *  / /_/ /  __/ /  __/ /_/  __/
 * /_____/\___/_/\___/\__/\___/
 *
 */

namespace MichielGerritsen\Revive\Test\Validate\Validators;

use MichielGerritsen\Revive\External\Amqp;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\FileSystem\ReadMysqlConfig;
use MichielGerritsen\Revive\Test\Fakes\CurrentWorkingDirectoryFake;
use MichielGerritsen\Revive\Validate\Validators\AmpqCredentialsAreValid;
use PHPUnit\Framework\TestCase;

class RabbitMqCredentialsAreValidTest extends TestCase
{
    public function testIsValidWhenNoAmpqCredentials()
    {
        $readMysqlConfigMock = $this->createMock(ReadMysqlConfig::class);
        $readMysqlConfigMock->method('read')->willReturn([]);

        /** @var AmpqCredentialsAreValid $instance */
        $instance = container()->make(AmpqCredentialsAreValid::class, [
            'config' => $readMysqlConfigMock,
        ]);

        $this->assertTrue($instance->validate());
    }

    /**
     * @param $connectionSuccess
     * @param $expected
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @testWith [true, true]
     *           [false, false]
     */
    public function testReturnsTheCorrectStatusDependingOnTheConnectionResult($connectionSuccess, $expected)
    {
        $readMysqlConfigMock = $this->createMock(ReadMysqlConfig::class);
        $readMysqlConfigMock->method('read')->willReturn([
            'amqp-host' => 'localhost',
            'amqp-port' => '15672',
            'amqp-user' => 'guest',
            'amqp-password' => 'guest',
        ]);

        $amqpMock = $this->createMock(Amqp::class);
        $amqpMock->expects($this->once())->method('testConnection')->willReturn($connectionSuccess);

        /** @var AmpqCredentialsAreValid $instance */
        $instance = container()->make(AmpqCredentialsAreValid::class, [
            'config' => $readMysqlConfigMock,
            'amqp' => $amqpMock,
        ]);

        $this->assertSame($expected, $instance->validate());
    }

    public function testGetErrors()
    {
        $this->assertCount(1, container()->make(AmpqCredentialsAreValid::class)->getErrors());
    }

    public function testShouldContinue()
    {
        $this->assertTrue(container()->make(AmpqCredentialsAreValid::class)->shouldContinue());
    }
}
