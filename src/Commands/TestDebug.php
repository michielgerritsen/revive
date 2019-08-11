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

use MichielGerritsen\Revive\Exceptions\FailingForUnknownReason;
use MichielGerritsen\Revive\Exceptions\InstanceFailingException;
use MichielGerritsen\Revive\Magento\ErrorOutput;
use MichielGerritsen\Revive\Magento\FixModule;
use MichielGerritsen\Revive\Magento\IntegrationTests;
use MichielGerritsen\Revive\Magento\ModuleManager;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestDebug extends Command
{
    protected static $defaultName = 'revive:test:debug';

    /**
     * @var CurrentWorkingDirectory
     */
    private $directory;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var FixModule
     */
    private $fixModule;

    /**
     * @var IntegrationTests
     */
    private $integrationTests;

    /**
     * @var ErrorOutput
     */
    private $errorOutput;

    public function __construct(
        CurrentWorkingDirectory $directory,
        ModuleManager $moduleManager,
        FixModule $fixModule,
        IntegrationTests $integrationTests,
        ErrorOutput $errorOutput
    ) {
        parent::__construct();

        $this->directory = $directory;
        $this->moduleManager = $moduleManager;
        $this->fixModule = $fixModule;
        $this->integrationTests = $integrationTests;
        $this->errorOutput = $errorOutput;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->directory->setFromInput($input);

        // Validate installation.

        // Tests dependencies

        // Place our code to read the exceptions.

        $output->writeln('<info>Patching your Magento installation</info>');
        $this->errorOutput->patch();

        // Create the module
        $this->moduleManager->createIntegrationTestModule();

        // Run the installation.

        $runs = 0;
        $failingInstance = null;
        while (true) {
            if (!$output->isVeryVerbose()) {
                $this->integrationTests->run();
            } else {
                $this->integrationTests->runVerbose($output);
            }

            if ($this->integrationTests->wasRunSuccessful()) {
                break;
            }

            $currentFailingInstance = $this->integrationTests->getFailingInstance();

            if ($currentFailingInstance && $currentFailingInstance == $failingInstance) {
                $output->writeln($this->integrationTests->getLogs());

                throw InstanceFailingException::withInstance($failingInstance);
            }

            if (!$currentFailingInstance) {
                throw new FailingForUnknownReason(
                    'It looks like there are no instances that are failing (anymore), but the test command still ' .
                    ' fails for unknown reasons. Usually this is caused by setup scripts that are failing'
                );
            }

            $this->fixModule->proxyDependenciesFor($currentFailingInstance);

            $output->writeln('Run #' . ++$runs);

            if ($runs == 20) {
                $output->writeln($this->integrationTests->getLogs());

                $output->writeln(
                    '<error>We tried to run the tests 20 times but without success. Please check the logs ' .
                    'to see what is going on. If they look good just try again</error>'
                );
                break;
            }
        }

        // Fix our manual fixes.
        $this->errorOutput->undo();

        // Remove the modules created by Magento.

        $output->writeln('<error>Shut it down</error>');
    }
}
