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

use Illuminate\Container\Container;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Validate\ValidateSetup;

function container() {
    static $container;

    if (!$container) {
        $container = new Container;
    }

    return $container;
}

container()->bind(ValidateSetup::class, function () {
    return new ValidateSetup(
        container()->make(CurrentWorkingDirectory::class),
        [
            container()->make(\MichielGerritsen\Revive\Validate\Validators\IsMagentoInstallation::class),
            container()->make(\MichielGerritsen\Revive\Validate\Validators\TheConfigFileExists::class),
            container()->make(\MichielGerritsen\Revive\Validate\Validators\PhpUnitFileExists::class),
            container()->make(\MichielGerritsen\Revive\Validate\Validators\MysqlCredentialsAreValid::class),
            container()->make(\MichielGerritsen\Revive\Validate\Validators\AmpqCredentialsAreValid::class),
        ]
    );
});
