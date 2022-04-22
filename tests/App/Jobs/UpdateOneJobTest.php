<?php
/**
 * Created by PhpStorm.
 * User: hugh.li
 * Date: 2021/8/14
 * Time: 16:38
 */

namespace Tests\App\Jobs;

use App\Jobs\UpdateOneJob;
use Tests\TestCase;
use Throwable;

class UpdateOneJobTest extends TestCase
{
    /**
     * @throws Throwable
     */
    public function test_handle()
    {
        $this->testJob(new UpdateOneJob([
            'url' => 'https://codeup.aliyun.com/hughcube/ms/user-php.git'
        ]));
    }
}
