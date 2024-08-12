<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class MigrateToNewYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'MigrateToNewYear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrates the system to new year';

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
        $dbName = DB::connection()->getDatabaseName();

        if (!$this->confirm('This will truncate      (' . $dbName . ')      Continue?')) {
            return;
        }

        if (!$this->confirm('Are you sure you want truncate (' . $dbName . ')!!!!?')) {
            return;
        }

        DB::transaction(function () {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

            DB::statement('TRUNCATE `activity_log`;');
            DB::statement('TRUNCATE `balance_transactions`;');
            DB::statement('TRUNCATE `balances`;');
            DB::statement('TRUNCATE `batches`;');
            DB::statement('TRUNCATE `checks`;');
            DB::statement('TRUNCATE `incinerations`;');
            DB::statement('TRUNCATE `incineration_batches`;');
            DB::statement('TRUNCATE `incineration_payments`;');
            DB::statement('TRUNCATE `inspections`;');
            DB::statement('TRUNCATE `media`;');
            DB::statement('TRUNCATE `payments`;');
            DB::statement('TRUNCATE `received_invoices`;');
            DB::statement('TRUNCATE `refunds`;');
            DB::statement('TRUNCATE `sensor_logs`;');
            DB::statement('TRUNCATE `swabs`;');
            DB::statement('TRUNCATE `transactions`;');
            // DB::statement('TRUNCATE `transaction_trucks`;');

            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        });

        $this->info('The command was successful!');
    }
}
