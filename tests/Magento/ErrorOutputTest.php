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

namespace MichielGerritsen\Revive\Test\Magento;

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Magento\ErrorOutput;
use MichielGerritsen\Revive\Test\Fakes\CurrentWorkingDirectoryFake;
use PHPUnit\Framework\TestCase;

class ErrorOutputTest extends TestCase
{
    /**
     * @return string
     */
    private function prepareStubs(): string
    {
        /** @var CurrentWorkingDirectory $directory */
        $directory = container()->make(CurrentWorkingDirectory::class);

        $filePath = $directory->get() . '/vendor/magento/framework/ObjectManager/Factory/AbstractFactory.php';
        $stub = file_get_contents(__DIR__ . '/../Stubs/Magento/Framework/ObjectManager/Factory/AbstractFactory.stub');

        mkdir($directory->get() . '/vendor/magento/framework/ObjectManager/Factory/', 0777, true);
        file_put_contents($filePath, $stub);

        return $filePath;
    }

    public function testThatOurCodeIsPlaced()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $filePath = $this->prepareStubs();

        /** @var ErrorOutput $instance */
        $instance = container()->make(ErrorOutput::class);
        $instance->patch();

        $this->assertContains(
            'throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));',
            file_get_contents($filePath)
        );
    }

    public function testThePatchIsUndone()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $filePath = $this->prepareStubs();

        /** @var ErrorOutput $instance */
        $instance = container()->make(ErrorOutput::class);
        $instance->patch();

        $this->assertContains(
            'throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));',
            file_get_contents($filePath)
        );

        $instance->undo();

        $this->assertNotContains(
            'throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));',
            file_get_contents($filePath)
        );
    }

    public function testRestoresFilesOnDestruction()
    {
        container()->singleton(CurrentWorkingDirectory::class, CurrentWorkingDirectoryFake::class);

        $filePath = $this->prepareStubs();

        /** @var ErrorOutput $instance */
        $instance = container()->make(ErrorOutput::class);
        $instance->patch();

        $this->assertContains(
            'throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));',
            file_get_contents($filePath)
        );

        $instance = null;

        $this->assertNotContains(
            'throw new \Exception(\'Failing command: \' . $key . \' instance: \' . $item[\'instance\'] . \' JSON: \' . json_encode([\'instance\' => $item[\'instance\']]));',
            file_get_contents($filePath)
        );
    }
}
