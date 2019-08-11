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

namespace MichielGerritsen\Revive\Magento;

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use Symfony\Component\Process\Process;

class FixModule
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

    public function proxyDependenciesFor($class)
    {
        $replacements = [
            '{$autoloadPath}' => $this->directory->get() . '/vendor/autoload.php',
            '{$diPath}' => $this->directory->get() . '/app/code/MichielGerritsen/ReviveFixes/etc/di.xml',
            '{$class}' => $class,
        ];

        $moduleFixerContent = str_replace(
            array_keys($replacements),
            array_values($replacements),
            file_get_contents(__DIR__ . '/../Stubs/ModuleFixer.stub')
        );

        $path = tempnam(sys_get_temp_dir(), 'revive');

        file_put_contents($path, $moduleFixerContent);
        file_put_contents($this->directory->get() . '/debugger.php', $moduleFixerContent);

        $process = new Process([PHP_BINARY, $path]);

        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > ' . $buffer;
            } else {
                echo 'OUT > ' . $buffer;
            }
        });

        unlink($path);
    }
}
