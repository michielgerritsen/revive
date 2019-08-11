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

namespace MichielGerritsen\Revive\Test\FileSystem;

use MichielGerritsen\Revive\FileSystem\TemporaryFile;
use PHPUnit\Framework\TestCase;

class TemporaryFileTest extends TestCase
{
    public function testGeneratesAnExistingFile()
    {
        /** @var TemporaryFile $instance */
        $instance = container()->make(TemporaryFile::class);

        $path = $instance->generate();

        $this->assertTrue(file_exists($path));
    }

    public function testDeletesFilesOnDestruction()
    {
        /** @var TemporaryFile $instance */
        $instance = container()->make(TemporaryFile::class);

        $path = $instance->generate();

        $instance = null;

        $this->assertFalse(file_exists($path));
    }
}
