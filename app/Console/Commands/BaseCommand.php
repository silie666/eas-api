<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Helper\ProgressBar;

abstract class BaseCommand extends Command
{
    /**
     * Call another console command.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function call($command, array $arguments = [])
    {
        if (is_subclass_of($command, SymfonyCommand::class)) {
            $command = $this->laravel->make($command)->getName();
        }

        return parent::call($command, $arguments);
    }

    /**
     * Call another console command.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function callWithInfo($command, array $arguments = [])
    {
        if (is_subclass_of($command, SymfonyCommand::class)) {
            $command = $this->laravel->make($command)->getName();
        }

        $this->info('↓↓↓↓↓↓↓↓↓↓ 开始执行其他任务 ↓↓↓↓↓↓↓↓↓↓');
        $result = parent::call($command, $arguments);
        $this->info('↑↑↑↑↑↑↑↑↑↑ 其他任务执行结束 ↑↑↑↑↑↑↑↑↑↑');

        return $result;
    }

    /**
     * Call another console command silently.
     *
     * @param string $command
     * @param array  $arguments
     *
     * @return int
     */
    public function callSilent($command, array $arguments = [])
    {
        if (is_subclass_of($command, SymfonyCommand::class)) {
            $command = $this->laravel->make($command)->getName();
        }

        return parent::callSilent($command, $arguments);
    }

    /**
     * Create progress bar.
     *
     * @param int         $count
     * @param string|null $message
     *
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function createProgressBar(int $count, string $message = null): ProgressBar
    {
        $processBar = $this->output->createProgressBar($count);
        $format     = '';
        if ($message) {
            $format .= '%message% ';
            $processBar->setMessage($message);
        }
        $seconds = $count ? '预计:%remaining% ' : '用时:0 sec';
        $format  .= '%current%/%max% %bar% %percent%% ' . $seconds . '  内存:%memory%';
        $processBar->setFormat($format . "\n");

        return $processBar;
    }
}
