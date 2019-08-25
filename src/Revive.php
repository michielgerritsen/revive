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

use MichielGerritsen\Revive\Application\Configure;
use MichielGerritsen\Revive\Commands\DbDump;
use MichielGerritsen\Revive\Commands\TestDebug;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use Symfony\Component\Console\Application;

container()->singleton(CurrentWorkingDirectory::class);

$application = new Application();
(new Configure())->options($application->getDefinition());

$application->add(container()->make(TestDebug::class));
$application->add(container()->make(DbDump::class));

$application->run();
