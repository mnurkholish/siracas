<?php

namespace Tests;

use Illuminate\Contracts\View\Engine;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app['view.engine.resolver']->register('blade', fn () => new class implements Engine
        {
            public function get($path, array $data = [])
            {
                return '';
            }
        });
    }
}
