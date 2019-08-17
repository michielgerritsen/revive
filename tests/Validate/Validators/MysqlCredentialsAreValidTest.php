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

use MichielGerritsen\Revive\External\Mysql;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\FileSystem\ReadMysqlConfig;
use MichielGerritsen\Revive\Test\Fakes\CurrentWorkingDirectoryFake;
use MichielGerritsen\Revive\Validate\Validators\MysqlCredentialsAreValid;
use PHPUnit\Framework\TestCase;

class MysqlCredentialsAreValidTest extends TestCase
{
    public function testShouldContinue()
    {
        $this->assertTrue(container()->make(MysqlCredentialsAreValid::class)->shouldContinue());
    }

    public function testGetErrors()
    {
        $this->assertNotEmpty(container()->make(MysqlCredentialsAreValid::class)->getErrors()[0]);
    }

    public function testFailsIfThereIsNoConnectionData()
    {
        $readMysqlConfigMock = $this->createMock(ReadMysqlConfig::class);
        $readMysqlConfigMock->method('read')->willReturn([
            'db-host' => '',
            'db-port' => '',
            'db-name' => '',
            'db-user' => '',
            'db-password' => '',
        ]);

        /** @var MysqlCredentialsAreValid $instance */
        $instance = container()->make(MysqlCredentialsAreValid::class, [
            'config' => $readMysqlConfigMock,
        ]);

        $this->assertFalse($instance->validate());
    }

    /**
     * @param $connectionResult
     * @param $expected
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     *
     * @testWith [false, false]
     *           [true, true]
     */
    public function testValidatesDependingOnTheStatus($connectionResult, $expected)
    {
        $mysqlMock = $this->createMock(Mysql::class);
        $mysqlMock->expects($this->once())->method('testConnection')->willReturn($connectionResult);

        $readMysqlConfigMock = $this->createMock(ReadMysqlConfig::class);
        $readMysqlConfigMock->method('read')->willReturn([
            'db-host' => 'localhost',
            'db-port' => '',
            'db-name' => 'database',
            'db-user' => '',
            'db-password' => '',
        ]);

        /** @var MysqlCredentialsAreValid $instance */
        $instance = container()->make(MysqlCredentialsAreValid::class, [
            'mysql' => $mysqlMock,
            'config' => $readMysqlConfigMock,
        ]);

        $this->assertSame($expected, $instance->validate());
    }
}
