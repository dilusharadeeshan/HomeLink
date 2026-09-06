<?php

namespace Tests;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }
}
