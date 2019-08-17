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

class ModuleManager
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

    public function getPath()
    {
        return $this->directory->get() . '/app/code/MichielGerritsen/ReviveFixes/';
    }

    public function createIntegrationTestModule()
    {
        // TODO: Check if the module exists.

        $modulePath = $this->getPath();

        if (file_exists($modulePath)) {
            return;
        }

        mkdir($modulePath . 'etc/', 0777, true);
        mkdir($modulePath . 'Test/', 0777, true);

        $stubPath = __DIR__ . '/../Stubs/MagentoModule/';
        $diXml = file_get_contents($stubPath . 'etc/di.stub');
        $moduleXml = file_get_contents($stubPath . 'etc/module.stub');
        $registrationPhp = file_get_contents($stubPath . 'registration.stub');
        $testPhp = file_get_contents($stubPath . '/Test/AlwaysSucceedingTest.stub');

        file_put_contents($modulePath . 'etc/di.xml', $diXml);
        file_put_contents($modulePath . 'etc/module.xml', $moduleXml);
        file_put_contents($modulePath . 'registration.php', $registrationPhp);
        file_put_contents($modulePath . 'Test/AlwaysSucceedingTest.php', $testPhp);
    }
}
