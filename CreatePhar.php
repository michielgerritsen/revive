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

ini_set('phar.readonly', 'off');

// The php.ini setting phar.readonly must be set to 0
$pharFile = 'revive.phar';

exec('mkdir build; cp -rf src build/src; cp -rf vendor build/vendor');

// clean up
if (file_exists($pharFile)) {
    unlink($pharFile);
}

if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

// create phar
$phar = new Phar($pharFile);

// creating our library using whole directory
$phar->buildFromDirectory('./build');

// pointing main file which requires all classes
$phar->setDefaultStub('src/Revive.php', 'src/Revive.php');

// plus - compressing it into gzip
$phar->compress(Phar::GZ);
$phar->compressFiles(Phar::GZ);

echo $pharFile . ' successfully created' . PHP_EOL;

if (file_exists($pharFile . '.gz')) {
    unlink($pharFile . '.gz');
}

exec('rm -rf build');
