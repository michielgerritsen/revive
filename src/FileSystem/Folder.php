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

class Folder
{
    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    public function __construct(
        CurrentWorkingDirectory $directory
    ) {
        $this->directory = $directory;
    }

    /**
     * @param string $directory
     */
    public function emptyFolder($directory)
    {
        foreach (glob($directory . '/*') as $directory) {
            $this->deleteFolder($directory);
        }
    }

    /**
     * @param string $directory
     */
    public function deleteFolder($directory)
    {
        $it = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }
}
