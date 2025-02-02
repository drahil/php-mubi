#!/usr/bin/env php

<?php

require __DIR__ . '/../vendor/autoload.php';

use drahil\PhpMubi\Commands\StatsCommand;

set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
});

try {
    $command = new StatsCommand();
    exit($command->run());
} catch (Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}

