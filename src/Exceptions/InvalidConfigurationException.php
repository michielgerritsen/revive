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

namespace MichielGerritsen\Revive\Exceptions;

class InvalidConfigurationException extends \Exception
{
    public static function configFileDoesNotExists()
    {
        return new static(
            'The config file does not exists. Please check `dev/tests/integration/etc/install-config-mysql.php`'
        );
    }

    public static function unableToParseConfig($message)
    {
        return new static(
            'We are unable to parse the configuration file: ' . $message
        );
    }

    public static function invalidEnvFile()
    {
        return new static(
            'There seems to be no connection data in the app/etc/env.php file.'
        );
    }
}
