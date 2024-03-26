<?php
declare(strict_types=1);

namespace App\Listeners\Course;

use Illuminate\Contracts\Queue\ShouldQueue as BaseShouldQueue;

class ShouldQueue implements BaseShouldQueue
{
    public string $queue = 'listen-course';
}