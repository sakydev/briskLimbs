<?php

namespace App\Logger;

use App\Services\FileService;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class ProcessingLogger
{
    private string $filename;
    private Logger $log;

    public function __construct(string $filename) {
        $this->filename = $filename;
        $this->log = new Logger($filename);
        $this->log->pushHandler(
            new StreamHandler(FileService::getLog($filename)), Logger::INFO
        );
    }

    public function debug(string $message): void
    {
        $this->log->debug("$this->filename => $message");
    }

    public function info(string $message): void
    {
        $this->log->info("$this->filename => $message");
    }

    public function notice(string $message): void
    {
        $this->log->notice("$this->filename => $message");
    }

    public function warning(string $message): void
    {
        $this->log->warning("$this->filename => $message");
    }

    public function error(string $message): void
    {
        $this->log->error("$this->filename => $message");
    }

    public function statusChange(string $status): void {
        $this->info("set video status => $status");
    }

    public function inputFile(string $input): void {
        $this->info("set input file => $input");
    }

    public function processedThumbnails(array $thumbnails, int $quality): void {
        $this->info("processed $quality thumbnails successfully => " . implode(',', $thumbnails));
    }

    public function processedVideos(array $videos, int $quality): void {
        $this->info("processed $quality videos successfully => " . implode(',', $videos));
    }
}
