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

use MichielGerritsen\Revive\External\Mysql;
use MichielGerritsen\Revive\FileSystem\ReadMysqlConfig;

class MysqlCredentialsAreValid implements ValidatorContract
{
    /**
     * @var ReadMysqlConfig
     */
    private $config;

    /**
     * @var Mysql
     */
    private $mysql;

    public function __construct(
        ReadMysqlConfig $config,
        Mysql $mysql
    ) {
        $this->config = $config;
        $this->mysql = $mysql;
    }

    public function validate(): bool
    {
        $config = $this->config->read();

        if (empty($config['db-host']) || empty($config['db-name'])) {
            return false;
        }

        return $this->mysql->testConnection(
            $config['db-host'],
            $config['db-port'],
            $config['db-name'],
            $config['db-user'],
            $config['db-password']
        );
    }

    public function shouldContinue(): bool
    {
        return true;
    }

    public function getErrors(): array
    {
        return [
            'Please check your mysql settings in `dev/tests/integration/etc/install-config-mysql.php` ' .
            'as these are invalid',
        ];
    }
}
