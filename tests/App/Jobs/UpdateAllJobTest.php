<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/8/14
 * Time: 16:38
 */

namespace Tests\App\Jobs;

use App\Jobs\UpdateAllJob;
use Tests\TestCase;
use Throwable;

class UpdateAllJobTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test_handle()
    {
        $this->testJob(new UpdateAllJob());
    }
}
