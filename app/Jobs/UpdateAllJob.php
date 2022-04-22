<?php

namespace App\Jobs;

use App\Services\Satis\Satis;
use Illuminate\Contracts\Bus\Dispatcher;

class UpdateAllJob extends AAAJob
{
    protected function action(): void
    {
        foreach (Satis::mergeBuiltPackages()->getPackages() as $package) {
            $this->trigger($package);
        }
    }

    protected function trigger($package)
    {
        $requestId = app(Dispatcher::class)->dispatch(UpdateOneJob::new($package));
        $this->info(sprintf('requestId:%s, url: %s', $requestId, $package['url']));
    }
}
