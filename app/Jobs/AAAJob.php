<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\PendingDispatch;

abstract class AAAJob extends \HughCube\Laravel\Knight\Queue\Job
{
    protected $logChannel = 'queue';

    public static function dispatchP(...$arguments): PendingDispatch
    {
        return static::dispatch(...$arguments)->onConnection('database');
    }
}
