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

namespace MichielGerritsen\Revive\Commands;

use MichielGerritsen\Revive\Exceptions\InvalidConfigurationException;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Magento\ShallowDatabaseBackup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class DbDump extends Command
{
    protected static $defaultName = 'db:dump';
    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;
    /**
     * @var ShallowDatabaseBackup
     */
    private $backup;

    public function __construct(
        CurrentWorkingDirectory $directory,
        ShallowDatabaseBackup $backup
    ) {
        parent::__construct();

        $this->directory = $directory;
        $this->backup = $backup;
    }

    protected function configure()
    {
        $description = 'Create a database dump that you can use to get started with your integration tests. ' .
            'WARNING: This dump is not a backup!';

        $this->setDescription($description);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->directory->setFromInput($input);

        $configuration = require $this->directory->get() . '/app/etc/env.php';

        if (!isset($configuration['db'], $configuration['db']['connection'])) {
            throw InvalidConfigurationException::invalidEnvFile();
        }

        $helper = $this->getHelper('question');
        $message = 'Warning: This method is meant as a quick and dirty solution to get integration tests up and ';
        $message .= 'running. Are you sure you want to continue? [Yn] ';
        $question = new ConfirmationQuestion($message, false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $path = $this->backup->execute($output, $configuration['db']['connection']);

        $output->writeln('');
        $output->writeln('<fg=green>The dump is created at ' . $path . '</>');
    }
}
