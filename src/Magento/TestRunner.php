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

use MichielGerritsen\Revive\Exceptions\FailingForUnknownReason;
use MichielGerritsen\Revive\Exceptions\InstanceFailingException;
use Symfony\Component\Console\Output\OutputInterface;

class TestRunner
{
    /**
     * @var IntegrationTests
     */
    private $integrationTests;

    /**
     * @var FixModule
     */
    private $fixModule;

    public function __construct(
        IntegrationTests $integrationTests,
        FixModule $fixModule
    ) {
        $this->integrationTests = $integrationTests;
        $this->fixModule = $fixModule;
    }

    public function execute(OutputInterface $output)
    {
        $runs = 0;
        $failingInstance = null;
        $patchedInstances = [];
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

            $patchedInstances[] = $currentFailingInstance;
            if (!$currentFailingInstance) {
                $output->writeln($this->integrationTests->getLogs());

                throw new FailingForUnknownReason(
                    'It looks like there are no instances that are failing (anymore), but the test command still ' .
                    'fails for unknown reasons. Usually this is caused by setup scripts that are failing'
                );
            }

            $this->fixModule->proxyDependenciesFor($currentFailingInstance);
            $output->writeln('<warning>The class ' . $currentFailingInstance . ' is patched.</warning>');

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
    }
}
