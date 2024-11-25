<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @return void
     */
    public function test_env_db_equals(): void
    {
        $this->assertEquals("pgsql", env("DB_CONNECTION"));
    }

    /**
     * @return void
     */
    public function test_env_timezone_equals(): void
    {
        $this->assertEquals("Europe/Moscow", env("APP_TIMEZONE"));
    }

    /**
     * @return void
     */
    public function test_config_auth_equals(): void
    {
        $this->assertEquals("jwt", config("auth.guards.api.driver"));
        $this->assertEquals("users", config("auth.guards.api.provider"));
    }

    /**
     * @return void
     */
    public function test_config_concurrency_equals(): void
    {
        $this->assertEquals("fork", config("concurrency.default"));
    }
}
