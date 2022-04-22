<?php

namespace App\Jobs;

use App\Services\Satis\Satis;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Process\Process;

class UpdateOneJob extends AAAJob
{
    #[ArrayShape([])]
    public function rules(): array
    {
        return [
            'url' => ['required', 'url'],
            'type' => ['string', 'default:git']
        ];
    }

    protected function action(): void
    {
        $start = microtime(true);
        $client = Satis::reset()
            ->addPackage($this->p('url'), $this->p('type'))
            ->dumpConfig();

        $process = new Process(
            [
                $client->getBin(),
                "build",
                $client->getSatisConfigFile(),
                $client->getBuildDir(),
                "--no-interaction",
                "--repository-url",
                $this->p('url')
            ],
            null,
            ["COMPOSER_MEMORY_LIMIT" => "-1", "COMPOSER_HOME" => $client->getComposerDir()],
            null,
            null
        );

        $process->mustRun(function ($type, $buffer) {
            $this->debug(trim($buffer));
        });
        $end = microtime(true);

        $duration = ($end - $start);
        $this->info(sprintf('url: %s, duration:%ss.', $this->p('url'), $duration));
    }
}
