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

namespace MichielGerritsen\Revive\Test\Fakes;

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use org\bovigo\vfs\vfsStream;

class CurrentWorkingDirectoryFake extends CurrentWorkingDirectory
{
    /**
     * @var \org\bovigo\vfs\vfsStreamDirectory
     */
    private $stream;

    public function stream()
    {
        if (!$this->stream) {
            $this->stream = vfsStream::setup('magentoDirectory');
        }

        return $this->stream;
    }

    public function get()
    {
        return $this->stream()->url();
    }
}
