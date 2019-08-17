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

namespace MichielGerritsen\Revive\Validate\Validators;

use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;

class IsMagentoInstallation implements ValidatorContract
{
    const REQUIRED_FILES = [
        'app/etc/config.php',
        'bin/magento',
        'pub/index.php',
        'vendor/autoload.php',
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

    public function validate(): bool
    {
        foreach (static::REQUIRED_FILES as $file) {
            if (!file_exists($this->directory->get() . '/' . $file)) {
                return false;
            }
        }

        return true;
    }

    public function shouldContinue(): bool
    {
        return false;
    }

    public function getErrors(): array
    {
        return [
            'The current directory (' . $this->directory->get() . ') is not a Magento directory',
        ];
    }
}
