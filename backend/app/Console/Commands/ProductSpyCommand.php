<?php

namespace App\Console\Commands;

use App\Services\ProductSpyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ProductSpyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:spy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ProductSpyService $productSpyService)
    {
        DB::connection()->disableQueryLog();

        $productSpyService->run();
    }
}
