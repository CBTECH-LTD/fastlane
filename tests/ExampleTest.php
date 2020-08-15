<?php

namespace CbtechLtd\Fastlane\Tests;

use Orchestra\Testbench\TestCase;
use CbtechLtd\Fastlane\FastlaneServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [FastlaneServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
