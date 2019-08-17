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
use MichielGerritsen\Revive\Validate\Validators\IsMagentoInstallation;
use PHPUnit\Framework\TestCase;

class IsMagentoInstallationTest extends TestCase
{
    public function testShouldContinue()
    {
        $this->assertFalse(container()->make(IsMagentoInstallation::class)->shouldContinue());
    }

    public function testGetErrors()
    {
        $this->assertCount(1, container()->make(IsMagentoInstallation::class)->getErrors());
    }

    /**
     * @testWith [0]
     *           [1]
     *           [2]
     *           [3]
     */
    public function testTheConfigFileShouldExists($unexisting)
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $directory = container()->make(CurrentWorkingDirectory::class);

        $files = IsMagentoInstallation::REQUIRED_FILES;
        unset($files[$unexisting]);

        foreach ($files as $file) {
            $path = $directory->get() . '/' . $file;
            mkdir(dirname($path), 0755, true);
            touch($path);
        }

        $this->assertFalse(container()->make(IsMagentoInstallation::class)->validate());
    }

    public function testValidatesIfAllFilesExists()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $directory = container()->make(CurrentWorkingDirectory::class);

        foreach (IsMagentoInstallation::REQUIRED_FILES as $file) {
            $path = $directory->get() . '/' . $file;
            mkdir(dirname($path), 0755, true);
            touch($path);
        }

        $this->assertTrue(container()->make(IsMagentoInstallation::class)->validate());
    }
}
