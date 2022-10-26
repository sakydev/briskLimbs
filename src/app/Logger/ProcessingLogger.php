<?php

namespace App\Logger;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProcessingLogger
{
    public function __construct(string $filename) {
        $this->filename = $filename;
        $this->log = new Logger($filename);
        $this->log->pushHandler(
            new StreamHandler(
                storage_path('logs/' . $filename . '.log')
            ), Logger::INFO
        );
    }

    public function debug(string $message): void
    {
        $this->log->debug($message);
    }

    public function info(string $message): void
    {
        $this->log->info($message);
    }

    public function notice(string $message): void
    {
        $this->log->notice($message);
    }

    public function warning(string $message): void
    {
        $this->log->warning($message);
    }

    public function error(string $message): void
    {
        $this->log->error($message);
    }
}
