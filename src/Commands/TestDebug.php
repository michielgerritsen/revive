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

use MichielGerritsen\Revive\Magento\ErrorOutput;
use MichielGerritsen\Revive\Magento\ModuleManager;
use MichielGerritsen\Revive\FileSystem\CurrentWorkingDirectory;
use MichielGerritsen\Revive\Magento\TestRunner;
use MichielGerritsen\Revive\Validate\ValidateSetup;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;

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
     * @var ErrorOutput
     */
    private $errorOutput;

    /**
     * @var ValidateSetup
     */
    private $validateSetup;

    /**
     * @var TestRunner
     */
    private $testRunner;

    public function __construct(
        CurrentWorkingDirectory $directory,
        ModuleManager $moduleManager,
        ErrorOutput $errorOutput,
        ValidateSetup $validateSetup,
        TestRunner $testRunner
    ) {
        parent::__construct();

        $this->directory = $directory;
        $this->moduleManager = $moduleManager;
        $this->errorOutput = $errorOutput;
        $this->validateSetup = $validateSetup;
        $this->testRunner = $testRunner;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->directory->setFromInput($input);

        $io = new SymfonyStyle($input, $output);

        // Validate installation.
        if (!$this->validateSetup->validate()) {
            $width = (new Terminal())->getWidth();

            $message = 'There are some errors found, please fix these before continuing:';

            $output->writeln('');
            $output->writeln('<error>' . str_repeat(' ', $width) . '</error>');
            $output->writeln('<error>  ' . $message . str_repeat(' ', $width - strlen($message) - 2) . '</error>');
            $output->writeln('<error>' . str_repeat(' ', $width) . '</error>');
            $io->listing($this->validateSetup->getErrors($output));
            return 255;
        }

        // Place our code to read the exceptions.
        $output->writeln('<info>Patching your Magento installation</info>');
        $this->errorOutput->patch();

        // Create the module
        $this->moduleManager->createIntegrationTestModule();

        // Run the installation.
        $output->writeln('<info>You installation is verified. We are now starting the first run. This can take quite a while.</info>');

        try {
            $this->testRunner->execute($output);
        } finally {
            // Fix our manual fixes.
            $this->errorOutput->undo();
        }

        $io->success('It looks like we succefully did a test run. Now go and build something awesome!');
    }
}
