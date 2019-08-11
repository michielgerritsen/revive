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

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Test\Fakes\CurrentWorkingDirectoryFake;
use MichielGerritsen\Revive\Validate\ValidateSetup;
use MichielGerritsen\Revive\Validate\Validators\PhpUnitFileExists;
use PHPUnit\Framework\TestCase;

class PhpUnitFileExistsTest extends TestCase
{
    public function testValidate()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);
        $directory = container()->make(CurrentWorkingDirectory::class)->get();

        mkdir($directory . '/dev/tests/integration', 0777, true);
        touch($directory . '/dev/tests/integration/phpunit.xml');

        /** @var PhpUnitFileExists $instance */
        $instance = container()->make(PhpUnitFileExists::class);

        $this->assertTrue($instance->validate());
    }

    public function testSupportsTheQuickIntegration()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);
        $directory = container()->make(CurrentWorkingDirectory::class)->get();

        mkdir($directory . '/dev/tests/quick-integration', 0777, true);
        touch($directory . '/dev/tests/quick-integration/phpunit.xml');

        /** @var PhpUnitFileExists $instance */
        $instance = container()->make(PhpUnitFileExists::class);

        $this->assertTrue($instance->validate());
    }
}
