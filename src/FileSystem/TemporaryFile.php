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

namespace MichielGerritsen\Revive\FileSystem;

class TemporaryFile
{
    /**
     * @var array
     */
    private $files = [];

    public function generate()
    {
        $path = tempnam(sys_get_temp_dir(), 'revive');

        $this->files[] = $path;

        return $path;
    }

    public function __destruct()
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }
}
