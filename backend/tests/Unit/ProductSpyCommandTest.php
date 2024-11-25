<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class ProductSpyCommandTest extends TestCase
{
    public const COMMAND_NAME = "product:spy";
    public const TARGET_HOUR = 11;
    public const TIME_PATTERN = "0 11 * * *";

    /**
     * @return void
     */
    public function test_product_spy_command_scheduled_at_target_time(): void
    {
        $schedule = app(Schedule::class);

        $isScheduled = false;

        foreach ($schedule->events() as $event) {
            if (str_contains($event->command, self::COMMAND_NAME) && $event->expression === self::TIME_PATTERN) {
                $isScheduled = true;
                break;
            }
        }

        $this->assertTrue($isScheduled);
    }

    /**
     * @return void
     */
    public function test_product_spy_command_runs_at_target_time(): void
    {
        $schedule = app(Schedule::class);

        Carbon::setTestNow(Carbon::createFromTime(self::TARGET_HOUR, 0));

        $isScheduledAtRightTime = false;

        foreach ($schedule->events() as $event) {
            if (str_contains($event->command, self::COMMAND_NAME) && $event->isDue($this->app)) {
                $isScheduledAtRightTime = true;
                break;
            }
        }

        $this->assertTrue($isScheduledAtRightTime);
    }

    /**
     * @return void
     */
    public function test_product_spy_command_does_not_run_at_wrong_time(): void
    {
        $schedule = app(Schedule::class);

        $thisEvent = null;
        foreach ($schedule->events() as $event) {
            if (str_contains($event->command, self::COMMAND_NAME)) {
                $thisEvent = $event;
                break;
            }
        }

        $this->assertTrue(!is_null($thisEvent));

        for ($h = 0; $h < 23; $h++) {
            if ($h === self::TARGET_HOUR) {
                continue;
            }
            Carbon::setTestNow(Carbon::createFromTime($h, 0));

            $this->assertFalse($thisEvent->isDue($this->app), "fail on " . $h . " hours");
        }
    }
}
