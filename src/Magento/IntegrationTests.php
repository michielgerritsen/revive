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
use MichielGerritsen\Revive\FileSystem\Folder;
use MichielGerritsen\Revive\FileSystem\TemporaryFile;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class IntegrationTests
{
    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    /**
     * @var Process
     */
    private $process;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var bool|string
     */
    private $path;

    /**
     * @var Folder
     */
    private $folder;
    /**
     * @var ModuleManager
     */
    private $moduleManager;

    public function __construct(
        CurrentWorkingDirectory $directory,
        TemporaryFile $temporaryFile,
        Folder $folder,
        ModuleManager $moduleManager
    ) {
        $this->directory = $directory;
        $this->temporaryFile = $temporaryFile;
        $this->folder = $folder;
        $this->moduleManager = $moduleManager;
    }

    private function createProcess()
    {
        $this->path = $this->temporaryFile->generate();

        $this->folder->emptyFolder($this->directory->get() . '/dev/tests/integration/tmp/');

        $this->process = new Process(
            [
                '../../../vendor/bin/phpunit',
                $this->moduleManager->getPath() . '/Test/',
            ],
            $this->directory->get() . '/dev/tests/integration/',
            ['DEBUG_TMPFILE' => $this->path],
            null,
            null
        );

        return $this->process;
    }

    public function run()
    {
        return $this->createProcess()->run();
    }

    public function runVerbose(OutputInterface $output)
    {
        $this->createProcess()->run(function ($type, $buffer) use ($output) {
            if (Process::ERR === $type) {
                $output->writeln('<error>' . $buffer . '</error>');
            } else {
                $output->writeln($buffer);
            }
        });
    }

    public function wasRunSuccessful()
    {
        return !$this->process->getExitCode();
    }

    public function getFailingInstance()
    {
        $json = json_decode(file_get_contents($this->path), JSON_OBJECT_AS_ARRAY);

        return $json['instance'];
    }

    public function getLogs()
    {
        return $this->process->getOutput();
    }
}
