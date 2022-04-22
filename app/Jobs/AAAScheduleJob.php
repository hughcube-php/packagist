<?php

namespace App\Jobs;

use App\Services\Satis\Satis;
use HughCube\Laravel\Knight\Queue\Jobs\CleanFilesJob;
use HughCube\Laravel\Knight\Queue\Jobs\PingJob;
use Illuminate\Contracts\Bus\Dispatcher;

class AAAScheduleJob extends \HughCube\Laravel\Knight\Queue\Jobs\ScheduleJob
{
    protected $logChannel = 'schedule';

    protected function pushJob($job)
    {
        if (empty($job->getLogChannel())) {
            $job->setLogChannel('queue');
        }
        parent::pushJob($job);
    }

    protected function fireJobIfDue(string $expression, $job)
    {
        if ($this->isDue($expression)) {
            app(Dispatcher::class)->dispatchNow($job);
        }
    }



    protected function cleanFilesJobHandler()
    {
        $this->pushJobIfDue('01 03 * * *', CleanFilesJob::new([
            'items' => [
                [
                    'max_days' => 7,
                    'pattern' => ['*.log', '*.log.*'],
                    'dir' => array_values(array_unique(array_filter([log_path(), storage_path('logs')]))),
                ],
                [
                    'max_days' => 7,
                    'pattern' => ['*'],
                    'dir' => [storage_path('framework/cache/data')]
                ],
                [
                    'max_days' => 90,
                    'pattern' => '*.zip',
                    'dir' => [Satis::getBuildDir()],
                ]
            ]
        ]));
    }

    protected function pingApiJobHandler(): void
    {
        $this->pushJobIfDue('*/2 * * * *', PingJob::new());
    }

    /**
     * 每天做一次全量更新
     */
    protected function updateAllJobHandler(): void
    {
        $this->pushJobIfDue('01 04 * * *', UpdateAllJob::new());
    }
}
