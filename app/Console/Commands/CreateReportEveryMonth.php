<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Report;
use App\Date;

class CreateReportEveryMonth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create:report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Report Every Month';

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
        $now = Carbon::now()->subMonth();
        // $now = Carbon::now();

        $reports = Report::whereHas('date', function ($q) use ($now) {
            $q->where('month', $now->month)->where('year', $now->year);
        })->get();

        $date = Date::orderBy('id', 'desc')->first();

        foreach ($reports as $report) {
            Report::create([
                'module_id' => $report->module_id,
                'project_id' => $report->project_id,
                'date_id' => $date->id,
                'application_object_used' => $report->application_object_used
            ]);
        }
    }
}
