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

namespace MichielGerritsen\Revive\Validate;

use MichielGerritsen\Revive\Exceptions\InvalidConfigurationException;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Validate\Validators\ValidatorContract;

class ValidateSetup
{
    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    /**
     * @var ValidatorContract[]
     */
    private $validators = [];

    public function __construct(
        CurrentWorkingDirectory $directory,
        array $validators = []
    ) {
        $this->directory = $directory;
        $this->validators = $validators;
    }

    public function validate()
    {
        $result = true;

        foreach ($this->validators as $validator) {
            if (!$validator->validate()) {
                $result = false;
            }
        }

        return $result;
    }
}
