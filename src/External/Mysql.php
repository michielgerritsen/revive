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

namespace MichielGerritsen\Revive\External;

class Mysql
{
    public function testConnection($host, $port, $name, $user, $password)
    {
        try {
            $this->getConnection($host, $port, $name, $user, $password);
            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function getConnection($host, $port, $name, $user, $password)
    {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $name;

        if ($port) {
            $dsn .= ';port=' . $port;
        }

        return new \PDO($dsn, $user, $password);
    }
}
