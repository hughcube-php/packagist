<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Throwable;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @throws Throwable
     */
    protected function testJob(object $job)
    {
        $job->handle();
        $this->assertTrue(true);
    }
}
