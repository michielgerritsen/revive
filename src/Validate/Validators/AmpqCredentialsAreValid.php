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

use MichielGerritsen\Revive\External\Amqp;
use MichielGerritsen\Revive\FileSystem\ReadMysqlConfig;

class AmpqCredentialsAreValid implements ValidatorContract
{
    /**
     * @var ReadMysqlConfig
     */
    private $config;

    /**
     * @var Amqp
     */
    private $amqp;

    public function __construct(
        ReadMysqlConfig $config,
        Amqp $amqp
    ) {
        $this->config = $config;
        $this->amqp = $amqp;
    }

    public function validate(): bool
    {
        $config = $this->config->read();

        if (empty($config['amqp-host']) ||
            empty($config['amqp-port']) ||
            empty($config['amqp-user']) ||
            empty($config['amqp-password'])
        ) {
            return true;
        }

        return $this->amqp->testConnection(
            $config['amqp-host'],
            $config['amqp-port'],
            $config['amqp-user'],
            $config['amqp-password']
        );
    }

    public function shouldContinue(): bool
    {
        return true;
    }

    public function getErrors(): array
    {
        return [
            'You have AMPQ variables in your `dev/tests/integration/etc/install-config-mysql.php` file, but these ' .
            'are invalid. If you don\'t plan on using AMPQ just remove them.'
        ];
    }
}
