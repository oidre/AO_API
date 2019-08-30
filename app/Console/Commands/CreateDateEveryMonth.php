<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class CreateDateEveryMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Date Every Month';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Carbon::setLocale('id');
        $now = Carbon::now();
        \App\Date::create([
            'full_date' => $now->format('Y-m-d'),
            'month' => $now->month,
            'month_name' => $now->monthName,
            'year' => $now->year,
          ]);
    }
}
