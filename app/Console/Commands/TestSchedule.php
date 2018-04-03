<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon;

class TestSchedule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var StringTool
     */
    protected $signature = 'test-schedule';

    /**
     * The console command description.
     *
     * @var StringTool
     */
    protected $description = 'Test Schedule .';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        @file_put_contents('/tmp/test_schedule', Carbon::now()->toDateTimeString() . PHP_EOL, FILE_APPEND);
        // $this->comment(Carbon::now()->toDateTimeString() . PHP_EOL);
    }
}
