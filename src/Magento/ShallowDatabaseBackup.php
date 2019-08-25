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

namespace MichielGerritsen\Revive\Magento;

use MichielGerritsen\Revive\External\Mysql;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use PDO;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ShallowDatabaseBackup
{
    const TABLES_DATA_TO_DUMP = [
        'customer_eav_attribute',
        'customer_eav_attribute_website',
        'customer_form_attribute',
        'directory_country',
        'directory_country_region',
        'directory_country_region_name',
        'directory_currency_rate',
        'indexer_state',
        'patch_list',
        'setup_module',
        'store',
        'store_group',
        'store_website',
    ];

    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    /**
     * @var Mysql
     */
    private $mysql;

    /**
     * @var array
     */
    private $tables = [];

    /**
     * @var PDO[]
     */
    private $connections = [];

    /**
     * @var array
     */
    private $output = [];

    public function __construct(
        CurrentWorkingDirectory $directory,
        Mysql $mysql
    ) {
        $this->directory = $directory;
        $this->mysql = $mysql;
    }

    public function execute(OutputInterface $output, array $connections)
    {
        $this->openConnections($connections);
        $totalTableCount = $this->getTotalTableCount();

        $progressBar = new ProgressBar($output, $totalTableCount);
        foreach ($this->connections as $name => $connection) {
            $this->dump($name, $connection, $progressBar);
        }

        $progressBar->finish();

        $this->makeSurePathExists('dev/tests/integration');

        $path = $this->directory->get() . '/dev/tests/integration/db-backup-' . date('Ymd-His') . '.sql';
        file_put_contents(
            $path,
            implode(PHP_EOL, $this->output)
        );

        return $path;
    }

    private function dump($name, PDO $connection, ProgressBar $progressBar)
    {
        foreach ($this->tables[$name] as $tableName) {
            $this->dumpTable($connection, $tableName);
            $progressBar->advance();
        }
    }

    private function getTotalTableCount(): int
    {
        $count = 0;
        foreach ($this->connections as $name => $connection) {
            $result = $connection->query('show tables');

            $this->tables[$name] = array_map( function ($row) {
                return $row[0];
            }, $result->fetchAll(PDO::FETCH_NUM));

            $count += count($this->tables[$name]);
        }

        return $count;
    }

    private function openConnections(array $connections)
    {
        foreach ($connections as $name => $connection) {
            $this->connections[$name] = $this->mysql->getConnection(
                $connection['host'],
                $connection['port'] ?? null,
                $connection['dbname'],
                $connection['username'],
                $connection['password']
            );
        }
    }

    /**
     * @param PDO $connection
     * @param $tableName
     */
    private function dumpTable(PDO $connection, $tableName)
    {
        $create = $connection->query('SHOW CREATE TABLE ' . $tableName);
        $this->output[] = $create->fetch()[1] . PHP_EOL;

        if (!in_array($tableName, static::TABLES_DATA_TO_DUMP)) {
            return;
        }

        $rows = $connection->query('select * from ' . $tableName);
        foreach ($rows->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $this->outputRow($tableName, $row);
        }

        $this->output[] = '';
    }

    private function outputRow($tableName, $row)
    {
        $output = 'INSERT INTO `' . $tableName . '` ';
        $output .= '(`' . implode('`, `', array_keys($row)) . '`) VALUES ';
        $output .= '("' . implode('", "', $row) . ');';

        $this->output[] = $output;
    }

    private function makeSurePathExists(string $path)
    {
        $previous = $this->directory->get() . '/';
        foreach (explode('/', $path) as $part) {
            $current = $previous . '/' . $part;
            $previous = $current;
            if (file_exists($current)) {
                continue;
            }

            mkdir($current);
        }
    }
}
