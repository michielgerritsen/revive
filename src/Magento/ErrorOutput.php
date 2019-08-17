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

class ErrorOutput
{
    /**
     * @var string
     */
    private $originalContents = null;

    private $paths = [
        'vendor/magento/framework/ObjectManager/Factory/AbstractFactory.php',
        'lib/internal/Magento/Framework/ObjectManager/Factory/AbstractFactory.php',
    ];

    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    public function __construct(
        CurrentWorkingDirectory $directory
    ) {
        $this->directory = $directory;
    }

    public function patch()
    {
        foreach ($this->paths as $path) {
            $this->patchFile($this->directory->get() . '/' . $path);
        }
    }

    private function patchFile($path)
    {
        if (!file_exists($path)) {
            return;
        }

        $contents = $this->originalContents = file_get_contents($path);

        if (strpos($contents, 'json_encode([\'instance\' => $item[\'instance\']])') !== false) {
            return;
        }

        $result = str_replace(
            '$array[$key] = $this->objectManager->get($item[\'instance\']);',
            'try {
                            $array[$key] = $this->objectManager->get($item[\'instance\']);
                        } catch (\Exception $exception) {
                            file_put_contents(
                                getenv(\'DEBUG_TMPFILE\'),
                                json_encode([\'instance\' => $item[\'instance\']])
                            );

                            throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));
                        }',
            $contents
        );

        file_put_contents($path, $result);
    }

    public function undo()
    {
        if (!$this->originalContents) {
            return;
        }

        foreach ($this->paths as $path) {
            $fullPath = $this->directory->get() . '/' . $path;

            if (file_exists($fullPath)) {
                file_put_contents($fullPath, $this->originalContents);
            }
        }
    }

    public function __destruct()
    {
        $this->undo();
    }
}
