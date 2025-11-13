<?php

declare(strict_types=1);

namespace Tests\System;

use Tests\TestCase;

final class CheckMySQLTest extends TestCase
{
    public function test_mysql_environment(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Skipping MySQL test because the database is not MySQL.');
        }

        // Comprobación que solo tiene sentido en MySQL
        $dbName = config('database.connections.mysql.database');
        $this->assertNotEmpty($dbName);

        echo "✅ Running test on MySQL database: {$dbName}\n";
    }
}
