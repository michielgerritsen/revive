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

class TheConfigFileExists implements ValidatorContract
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

    public function shouldContinue(): bool
    {
        return false;
    }

    public function validate(): bool
    {
        return file_exists($this->directory->get() . '/dev/tests/integration/etc/install-config-mysql.php');
    }

    public function getErrors(): array
    {
        return [
            'The `dev/tests/integration/etc/install-config-mysql.php` file is missing. See this page on how to prepare to run the ' .
            'integration tests: https://devdocs.magento.com/guides/v2.3/test/integration/integration_test_execution.html'
        ];
    }
}
