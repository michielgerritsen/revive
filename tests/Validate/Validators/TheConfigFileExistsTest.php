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
use MichielGerritsen\Revive\Validate\Validators\TheConfigFileExists;
use PHPUnit\Framework\TestCase;

class TheConfigFileExistsTest extends TestCase
{
    public function testValidate()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $directory = container()->make(CurrentWorkingDirectory::class)->get();

        mkdir($directory . '/dev/tests/integration/etc', 0777, true);
        touch($directory . '/dev/tests/integration/etc/install-config-mysql.php');

        /** @var TheConfigFileExists $instance */
        $instance = container()->make(TheConfigFileExists::class);

        $this->assertTrue($instance->validate());
    }

    public function testGetErrors()
    {
        /** @var TheConfigFileExists $instance */
        $instance = container()->make(TheConfigFileExists::class);

        $errors = $instance->getErrors();

        $this->assertContains(
            'The `dev/tests/integration/etc/install-config-mysql.php` file is missing.',
            $errors[0]
        );
    }

    public function testShouldContinue()
    {
        $this->assertFalse(container()->make(TheConfigFileExists::class)->shouldContinue());
    }
}
