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

use Symfony\Component\Process\Process;

class ReadMysqlConfig
{
    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    /**
     * @var TemporaryFile
     */
    private $temporaryFile;

    /**
     * @var array
     */
    private $contents = [];

    /**
     * @var array
     */
    const REQUIRED_KEYS = [
        'db-host',
        'db-port',
        'db-user',
        'db-password',
        'db-name',
        'db-prefix',
        'amqp-host',
        'amqp-port',
        'amqp-user',
        'amqp-password'
    ];

    public function __construct(
        CurrentWorkingDirectory $directory,
        TemporaryFile $temporaryFile
    ) {
        $this->directory = $directory;
        $this->temporaryFile = $temporaryFile;
    }

    /**
     * @param string $contents
     * @return array
     * @throws \Exception
     */
    public function read()
    {
        if ($this->contents) {
            return $this->contents;
        }

        $path = $this->temporaryFile->generate();

        file_put_contents(
            $path,
            file_get_contents(__DIR__ . '/../Stubs/ReadConfig.stub')
        );

        $process = new Process(
            [PHP_BINARY, $path],
            $this->directory->get()
        );

        $process->run();

        $output = $process->getOutput();
        $contents = $this->fillBlanks(json_decode($output, JSON_OBJECT_AS_ARRAY));

        if ($process->getExitCode()) {
            throw new \Exception('Unable to get mysql credentials: ' . PHP_EOL . $process->getOutput());
        }

        $this->contents = $contents;
        return $contents;
    }

    private function fillBlanks($contents)
    {
        foreach (static::REQUIRED_KEYS as $key) {
            if (!array_key_exists($key, $contents)) {
                $contents[$key] = '';
            }
        }

        return $contents;
    }
}
