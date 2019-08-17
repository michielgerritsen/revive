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

namespace MichielGerritsen\Revive\Test\Service\FileSystem;

use MichielGerritsen\Revive\Application\Configure;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\StringInput;

class CurrentWorkingDirectoryTest extends TestCase
{
    public function testWeCanSetTheDirectory()
    {
        container()->flush();

        /** @var CurrentWorkingDirectory $instance */
        $instance = container()->make(CurrentWorkingDirectory::class);

        $input = new StringInput('--root-dir=/test/directory/path');
        $definition = new InputDefinition;
        container()->make(Configure::class)->options($definition);
        $input->bind($definition);

        $instance->setFromInput($input);

        $this->assertEquals('/test/directory/path', $instance->get());
    }
}
