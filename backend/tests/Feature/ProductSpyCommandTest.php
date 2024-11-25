<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductSpyCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function test_product_spy_command_success_call(): void
    {
        $this->artisan('product:spy')->assertExitCode(0);
        $this->artisan('product:spy')->assertSuccessful();
    }
}
