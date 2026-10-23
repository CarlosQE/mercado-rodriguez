<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** Aborta antes de que RefreshDatabase toque la base si no es la de pruebas. */
    protected function setUpTraits()
    {
        $base = config('database.connections.' . config('database.default') . '.database');

        if (! str_ends_with((string) $base, '_test')) {
            throw new \RuntimeException("Las pruebas solo corren sobre una base *_test (actual: {$base}).");
        }

        return parent::setUpTraits();
    }
}
