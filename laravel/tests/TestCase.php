<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /** IMPORTANTE AL EJECUTAR TESTS DE INTEGRACIÓN Y DE FEATURE SOBRE SQLITE EN MEMORIA: 
     *  Si se ejecutan los tests desde el contenedor Laravel, "docker compose exec laravel bash"
     *  y éste tiene esta configuración en el archivo "docker-compose.yml": DB_CONNECTION: mysql,
     *  se estárán ejecutando sobre la base de datos de desarrollo, por lo que ésta podría borrarse.
     *  Para evitarlo, se ha creado esta protección, que paraliza la ejecución y muestra un mensaje.
     *  La forma de ejecutar los tests será desde el contenedor PHP, "docker compose exec php bash"
     *  de manera que se pueda aplicar la configuración establecida en el archivo "phpunit.xml" que
     *  hay en la carpeta laravel, y que indica que los tests correrán contra SQLite en memoria, o contra
     *  mysql, o lo que nosotros queramos.
     *  Por eso, es IMPORTANTE, en DOCKER, ejecutar los tests desde el contenedor php, en lugar que hacerlo
     *  desde el contenedor de Laravel, o al menos tener en cuenta este comportamiento.
     */

    // use DatabaseTransactions; // El uso de este trait con SQLite en memoria, provoca falsos errores
    use RefreshDatabase; // Para tests con SQLite en memoria, se debe usar RefreshDatabase.

    // PROTECCIÓN SÓLO SI QUEREMOS EJECUTAR TESTS EN SQLITE EN MEMORIA: Verificar que estamos usando SQLite en memoria
    // Esto asegura que si intentas correr un test en MySQL, fallará inmediatamente antes de tocar nada.
    /* protected function setUp(): void
    {
        parent::setUp();
        $connection = config('database.default');
        $database = config('database.connections.sqlite.database');
        if ($connection !== 'sqlite' || $database !== ':memory:') {
            throw new \RuntimeException(
                "⚠️ Tests MUST use SQLite in memory!\n" .
                "  🔌 Current connection: {$connection}\n" .
                "  📚 Current database: {$database}\n" .
                "  💡 Perhaps you are running the tests from the Laravel container.\n" .
                "  🚨 You must run them from the PHP container.\n" .
                "  If you notice that the Laravel container keeps restarting after running these tests,\n" .
                "  run docker compose down -v and then bring all the containers back up with docker compose up -d\n"
            );
        }
    } */
}