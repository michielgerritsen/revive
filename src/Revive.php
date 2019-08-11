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

require __DIR__ . '/../vendor/autoload.php';

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;

container()->singleton(CurrentWorkingDirectory::class);

$application = new Application();
(new \MichielGerritsen\Revive\Application\Configure())->options($application->getDefinition());

foreach(glob(__DIR__ . '/Commands/*.php') as $command) {
    $parts = explode('/', rtrim($command, '.php'));
    $command = end($parts);
    $class = '\\MichielGerritsen\\Revive\\Commands\\' . $command;

    $application->add(container()->make($class));
}

$application->run();
