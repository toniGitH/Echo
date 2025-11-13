<?php

declare(strict_types=1);

namespace Tests\System;

use Tests\TestCase;

final class CheckSQLiteTest extends TestCase
{
    public function test_sqlite_in_memory_environment(): void
    {
        if (config('database.default') !== 'sqlite') {
            $this->markTestSkipped('Skipping SQLite test because the database is not SQLite.');
        }

        // Comprobación que solo tiene sentido en SQLite en memoria
        $dbName = config('database.connections.sqlite.database');
        $this->assertEquals(':memory:', $dbName);

        echo "✅ Running test on SQLite in-memory database: {$dbName}\n";
    }
}
